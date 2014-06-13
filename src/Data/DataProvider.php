<?php
/**
 * DataProvider
 *
 * ORM integration adapted from Doctrine ORM Service Provider by Dragonfly Development Inc,
 * which is licensed under a MIT license: https://github.com/dflydev/dflydev-doctrine-orm-service-provider
 */

namespace Layer\Data;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Timestampable\TimestampableListener;
use Gedmo\Tree\TreeListener;
use Layer\Data\Metadata\Query\GetEditablePropertiesQuery;
use Layer\Data\Metadata\Query\GetEntityCrudQuery;
use Layer\Data\Metadata\Query\GetEntityHumanNameQuery;
use Layer\Data\Metadata\Query\GetEntityNameQuery;
use Layer\Data\Metadata\Query\GetPropertyLabelQuery;
use Layer\Data\Metadata\Query\GetPropertyOrmQuery;
use Layer\Data\Metadata\Query\GetTitlePropertyQuery;
use Layer\Data\Metadata\Query\GetVisiblePropertiesQuery;
use Layer\Data\Metadata\Query\GetVisiblePropertyLabelsQuery;
use Layer\Data\Metadata\Query\IsPropertyEditableQuery;
use Layer\Data\Metadata\Query\IsPropertyVisibleQuery;
use Layer\Data\Metadata\Query\IsTitlePropertyQuery;
use Layer\Data\Metadata\QueryCollection;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

class DataProvider implements ServiceProviderInterface {

	public function register(Application $app) {

		$app->register(new DoctrineServiceProvider());

		$initializer = $app['dbs.options.initializer'];
		$app['dbs.options.initializer'] = $app->protect(function() use($app, $initializer) {
			$app['dbs.options'] = $app['config']->read('database');
			$initializer();
		});

		foreach ($this->_getOrmDefaults($app) as $key => $value) {
			if (!isset($app[$key])) {
				$app[$key] = $value;
			}
		}

		$app['annotations.initializer'] = $app->protect(function() use($app) {
			static $initialized = false;
			if($initialized) {
				return;
			}
			$initialized = true;

			AnnotationRegistry::registerLoader([$app['class_loader'], 'loadClass']);
		});

		$app['annotations.base_reader'] = $app->share(function() use($app) {
			$app['annotations.initializer']();
			return new AnnotationReader();
		});

		$app['annotations.cache'] = $app->share(function() use($app) {
			return $app['orm.cache.locator']('default', 'annotations', []);
		});

		$app['annotations.reader'] = $app->share(function() use($app) {
			$app['annotations.initializer']();
			return new CachedReader($app['annotations.base_reader'], $app['annotations.cache'], false);
		});

		$app['annotations.loader'] = $app->share(function() use($app) {
			return new AnnotationLoader($app['annotations.reader']);
		});

		$app['orm.em.default_options'] = [
			'connection' => 'default',
			'mappings' => [],
			'types' => []
		];

		$app['orm.em.listeners.sluggable'] = $app->share(function() {
			return new SluggableListener();
		});

		$app['orm.em.listeners.timestampable'] = $app->share(function() {
			return new TimestampableListener();
		});

		$app['orm.em.listeners.tree'] = $app->share(function() {
			return new TreeListener();
		});

		$app['orm.ems.options.initializer'] = $app->protect(function () use ($app) {
			static $initialized = false;
			if ($initialized) {
				return;
			}
			$initialized = true;

			$app['annotations.initializer']();

			if (!isset($app['orm.ems.options'])) {
				$app['orm.ems.options'] = ['default' => isset($app['orm.em.options']) ? $app['orm.em.options'] : []];
			}

			$tmp = $app['orm.ems.options'];
			foreach ($tmp as $name => &$options) {
				$options = array_replace($app['orm.em.default_options'], $options);

				if (!isset($app['orm.ems.default'])) {
					$app['orm.ems.default'] = $name;
				}
			}
			$app['orm.ems.options'] = $tmp;
		});

		$app['orm.em_name_from_param_key'] = $app->protect(function ($paramKey) use ($app) {
			$app['orm.ems.options.initializer']();

			if (isset($app[$paramKey])) {
				return $app[$paramKey];
			}

			return $app['orm.ems.default'];
		});

		$app['orm.ems'] = $app->share(function($app) {
			$app['orm.ems.options.initializer']();

			$ems = new \Pimple();
			foreach ($app['orm.ems.options'] as $name => $options) {
				if ($app['orm.ems.default'] === $name) {
					// we use shortcuts here in case the default has been overridden
					$config = $app['orm.em.config'];
				} else {
					$config = $app['orm.ems.config'][$name];
				}

				$ems[$name] = $app->share(function ($ems) use ($app, $options, $config) {
					return EntityManager::create(
						$app['dbs'][$options['connection']],
						$config,
						$app['dbs.event_manager'][$options['connection']]
					);
				});
			}

			return $ems;
		});

		$app['orm.ems.config'] = $app->share(function($app) {
			$app['orm.ems.options.initializer']();

			$configs = new \Pimple();
			foreach ($app['orm.ems.options'] as $name => $options) {
				$config = new Configuration;

				$app['orm.cache.configurer']($name, $config, $options);

				$config->setProxyDir($app['orm.proxies_dir']);
				$config->setProxyNamespace($app['orm.proxies_namespace']);
				$config->setAutoGenerateProxyClasses($app['orm.auto_generate_proxies']);



				$chain = $app['orm.mapping_driver_chain.locator']($name);
				foreach ((array) $options['mappings'] as $entity) {
					if (!is_array($entity)) {
						throw new \InvalidArgumentException(
							"The 'orm.em.options' option 'mappings' should be an array of arrays."
						);
					}

					if (isset($entity['alias'])) {
						$config->addEntityNamespace($entity['alias'], $entity['namespace']);
					}

					switch ($entity['type']) {
						case 'annotation':
							$useSimpleAnnotationReader =
								isset($entity['use_simple_annotation_reader'])
									? $entity['use_simple_annotation_reader']
									: false;
							$driver = $config->newDefaultAnnotationDriver([], $useSimpleAnnotationReader);
							$chain->addDriver($driver, $entity['namespace']);
							break;
						case 'yml':
							$driver = new YamlDriver($entity['path']);
							$chain->addDriver($driver, $entity['namespace']);
							break;
						case 'xml':
							$driver = new XmlDriver($entity['path']);
							$chain->addDriver($driver, $entity['namespace']);
							break;
						case 'php':
							$driver = new StaticPHPDriver($entity['path']);
							$chain->addDriver($driver, $entity['namespace']);
							break;
						default:
							throw new \InvalidArgumentException(sprintf('"%s" is not a recognized driver', $entity['type']));
							break;
					}
				}
				$config->setMetadataDriverImpl($chain);
				$app['orm.rm']->initializeConfiguration($config);

				foreach ((array) $options['types'] as $typeName => $typeClass) {
					if (Type::hasType($typeName)) {
						Type::overrideType($typeName, $typeClass);
					} else {
						Type::addType($typeName, $typeClass);
					}
				}

				$configs[$name] = $config;
			}

			return $configs;
		});

		$app['orm.cache.configurer'] = $app->protect(function($name, Configuration $config, $options) use ($app) {
			$config->setMetadataCacheImpl($app['orm.cache.locator']($name, 'metadata', $options));
			$config->setQueryCacheImpl($app['orm.cache.locator']($name, 'query', $options));
			$config->setResultCacheImpl($app['orm.cache.locator']($name, 'result', $options));
		});

		$app['orm.cache.locator'] = $app->protect(function($name, $cacheName, $options) use ($app) {
			$cacheNameKey = $cacheName . '_cache';

			if (!isset($options[$cacheNameKey])) {
				$options[$cacheNameKey] = $app['orm.default_cache'];
			}

			if (isset($options[$cacheNameKey]) && !is_array($options[$cacheNameKey])) {
				$options[$cacheNameKey] = [
					'driver' => $options[$cacheNameKey],
				];
			}

			if (!isset($options[$cacheNameKey]['driver'])) {
				throw new \RuntimeException("No driver specified for '$cacheName'");
			}

			$driver = $options[$cacheNameKey]['driver'];

			$cacheInstanceKey = 'orm.cache.instances.'.$name.'.'.$cacheName;
			if (isset($app[$cacheInstanceKey])) {
				return $app[$cacheInstanceKey];
			}

			$cache = $app['orm.cache.factory']($driver, $options[$cacheNameKey]);

			if(isset($options['cache_namespace']) && $cache instanceof CacheProvider) {
				$cache->setNamespace($options['cache_namespace']);
			}

			return $app[$cacheInstanceKey] = $cache;
		});

		$app['orm.cache.factory.backing_memcache'] = $app->protect(function() {
			return new \Memcache;
		});

		$app['orm.cache.factory.memcache'] = $app->protect(function($cacheOptions) use ($app) {
			if (empty($cacheOptions['host']) || empty($cacheOptions['port'])) {
				throw new \RuntimeException('Host and port options need to be specified for memcache cache');
			}

			$memcache = $app['orm.cache.factory.backing_memcache']();
			$memcache->connect($cacheOptions['host'], $cacheOptions['port']);

			$cache = new MemcacheCache;
			$cache->setMemcache($memcache);

			return $cache;
		});

		$app['orm.cache.factory.backing_memcached'] = $app->protect(function() {
			return new \Memcached;
		});

		$app['orm.cache.factory.memcached'] = $app->protect(function($cacheOptions) use ($app) {
			if (empty($cacheOptions['host']) || empty($cacheOptions['port'])) {
				throw new \RuntimeException('Host and port options need to be specified for memcached cache');
			}

			$memcached = $app['orm.cache.factory.backing_memcached']();
			$memcached->addServer($cacheOptions['host'], $cacheOptions['port']);

			$cache = new MemcachedCache;
			$cache->setMemcached($memcached);

			return $cache;
		});

		$app['orm.cache.factory.backing_redis'] = $app->protect(function() {
			return new \Redis;
		});

		$app['orm.cache.factory.redis'] = $app->protect(function($cacheOptions) use ($app) {
			if (empty($cacheOptions['host']) || empty($cacheOptions['port'])) {
				throw new \RuntimeException('Host and port options need to be specified for redis cache');
			}

			$redis = $app['orm.cache.factory.backing_redis']();
			$redis->connect($cacheOptions['host'], $cacheOptions['port']);

			$cache = new RedisCache;
			$cache->setRedis($redis);

			return $cache;
		});

		$app['orm.cache.factory.array'] = $app->protect(function() {
			return new ArrayCache;
		});

		$app['orm.cache.factory.apc'] = $app->protect(function() {
			return new ApcCache;
		});

		$app['orm.cache.factory.xcache'] = $app->protect(function() {
			return new XcacheCache;
		});

		$app['orm.cache.factory.filesystem'] = $app->protect(function($cacheOptions) {
			if (empty($cacheOptions['path'])) {
				throw new \RuntimeException('FilesystemCache path not defined');
			}
			return new FilesystemCache($cacheOptions['path']);
		});

		$app['orm.cache.factory'] = $app->protect(function($driver, $cacheOptions) use ($app) {
			switch ($driver) {
				case 'array':
					return $app['orm.cache.factory.array']();
				case 'apc':
					return $app['orm.cache.factory.apc']();
				case 'xcache':
					return $app['orm.cache.factory.xcache']();
				case 'memcache':
					return $app['orm.cache.factory.memcache']($cacheOptions);
				case 'memcached':
					return $app['orm.cache.factory.memcached']($cacheOptions);
				case 'filesystem':
					// @todo Set this as a default somewhere else
					if(!isset($cacheOptions['path'])) {
						$cacheOptions['path'] = $app['paths.cache'] . '/doctrine';
					}
					return $app['orm.cache.factory.filesystem']($cacheOptions);
				case 'redis':
					return $app['orm.cache.factory.redis']($cacheOptions);
				default:
					throw new \RuntimeException("Unsupported cache type '$driver' specified");
			}
		});

		$app['orm.mapping_driver_chain.locator'] = $app->protect(function($name = null) use ($app) {
			$app['orm.ems.options.initializer']();

			if (null === $name) {
				$name = $app['orm.ems.default'];
			}

			$cacheInstanceKey = 'orm.mapping_driver_chain.instances.'.$name;
			if (isset($app[$cacheInstanceKey])) {
				return $app[$cacheInstanceKey];
			}

			return $app[$cacheInstanceKey] = $app['orm.mapping_driver_chain.factory']($name);
		});

		$app['orm.mapping_driver_chain.factory'] = $app->protect(function($name) use ($app) {
			return new MappingDriverChain;
		});

		$app['orm.add_mapping_driver'] = $app->protect(function(MappingDriver $mappingDriver, $namespace, $name = null) use ($app) {
			$app['orm.ems.options.initializer']();

			if (null === $name) {
				$name = $app['orm.ems.default'];
			}

			$driverChain = $app['orm.mapping_driver_chain.locator']($name);
			$driverChain->addDriver($mappingDriver, $namespace);
		});

		$app['orm.em'] = $app->share(function($app) {
			$ems = $app['orm.ems'];

			$em = $ems[$app['orm.ems.default']];

			foreach($app['orm.em.listeners'] as $listener) {
				$em->getEventManager()->addEventSubscriber($app['orm.em.listeners.' . $listener]);
			}

			return $em;
		});

		$app['orm.em.config'] = $app->share(function($app) {
			$configs = $app['orm.ems.config'];

			return $configs[$app['orm.ems.default']];
		});

		$app['orm.proxies_dir'] = $app['paths.cache'] . '/doctrine/proxies';
		$app['orm.auto_generate_proxies'] = true;

		$app['metadata.queries.getEntityName'] = $app->share(function() use($app) {
			return new GetEntityNameQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getEntityCrud'] = $app->share(function() use($app) {
			return new GetEntityCrudQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getPropertyLabel'] = $app->share(function() use($app) {
			return new GetPropertyLabelQuery($app['annotations.reader'], $app['inflector']);
		});

		$app['metadata.queries.getPropertyOrm'] = $app->share(function() use($app) {
			return new GetPropertyOrmQuery($app['annotations.reader']);
		});

		$app['metadata.queries.isPropertyVisible'] = $app->share(function() use($app) {
			return new IsPropertyVisibleQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getVisibleProperties'] = $app->share(function() use($app) {
			return new GetVisiblePropertiesQuery($app['metadata.queries.isPropertyVisible']);
		});

		$app['metadata.queries.getVisiblePropertyLabels'] = $app->share(function() use($app) {
			return new GetVisiblePropertyLabelsQuery(
				$app['metadata.queries.getVisibleProperties'],
				$app['metadata.queries.getPropertyLabel']
			);
		});

		$app['metadata.queries.isPropertyEditable'] = $app->share(function() use($app) {
			return new IsPropertyEditableQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getEditableProperties'] = $app->share(function() use($app) {
			return new GetEditablePropertiesQuery($app['metadata.queries.isPropertyEditable']);
		});

		$app['metadata.queries.isTitleProperty'] = $app->share(function() use($app) {
			return new IsTitlePropertyQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getTitleProperty'] = $app->share(function() use($app) {
			return new GetTitlePropertyQuery($app['metadata.queries.isTitleProperty']);
		});

		$app['metadata.queries.getEntityHumanName'] = $app->share(function() use($app) {
			return new GetEntityHumanNameQuery($app['annotations.reader'], $app['inflector']);
		});

		$app['metadata.queries'] = $app->share(function() use($app) {
			$collection = new QueryCollection();
			$collection
				->registerQuery($app['metadata.queries.getEntityName'])
				->registerQuery($app['metadata.queries.getEntityCrud'])
				->registerQuery($app['metadata.queries.getPropertyLabel'])
				->registerQuery($app['metadata.queries.getPropertyOrm'])
				->registerQuery($app['metadata.queries.isPropertyVisible'])
				->registerQuery($app['metadata.queries.getVisibleProperties'])
				->registerQuery($app['metadata.queries.getVisiblePropertyLabels'])
				->registerQuery($app['metadata.queries.isPropertyEditable'])
				->registerQuery($app['metadata.queries.getEditableProperties'])
				->registerQuery($app['metadata.queries.isTitleProperty'])
				->registerQuery($app['metadata.queries.getTitleProperty'])
				->registerQuery($app['metadata.queries.getEntityHumanName']);
			return $collection;
		});

		$app['orm.rm'] = $app->share(function() use($app) {
			return new RepositoryManager($app['dispatcher'], $app['metadata.queries']);
		});

		$app->register(new ValidatorServiceProvider());

		$app['validator.mapping.class_metadata_factory'] = $app->share(function() use($app) {
			return new LazyLoadingMetadataFactory($app['annotations.loader']);
		});

		$app['orm.manager_registry'] = $app->share(function() use($app) {
			$managerRegistry = new ManagerRegistry(null, [], ['orm.em'], null, null, $app['orm.proxies_namespace']);
			$managerRegistry->setContainer($app);
			return $managerRegistry;
		});

		$app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions) use($app) {
			$extensions[] = new DoctrineOrmExtension($app['orm.manager_registry']);
			return $extensions;
		}));

		$app['console.commands.schema'] = $app->share(function() {
			return new SchemaCommand();
		});

		$app['console'] = $app->share($app->extend('console', function(\Knp\Console\Application $consoleApp) use($app) {
			$consoleApp->add($app['console.commands.schema']);
			return $consoleApp;
		}));

	}

	/**
	 * @param Application $app
	 * @return array
	 */
	protected function _getOrmDefaults(Application $app) {
		return [
			// @todo orm.proxies_dir doesn't seem to get used
			'orm.proxies_dir' => $app['paths.cache'] . '/doctrine/proxies',
			'orm.auto_generate_proxies' => true,
			'orm.proxies_namespace' => 'Doctrine\Common\Proxy\Proxy',
			'orm.default_cache' => 'array',
			'orm.em.options' => [
				'mappings' => [
					[
						'type' => 'annotation',
						'namespace' => 'Layer'
					]
				]
			],
			'orm.em.listeners' => ['sluggable', 'timestampable', 'tree']
		];
	}

	public function boot(Application $app) {
	}

}