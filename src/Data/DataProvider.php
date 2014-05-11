<?php

namespace Layer\Data;

use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\ServiceProviderInterface;

class DataProvider implements ServiceProviderInterface {

	public function register(Application $app) {

		$app->register(new DoctrineServiceProvider());

		$initializer = $app['dbs.options.initializer'];
		$app['dbs.options.initializer'] = $app->protect(function() use($app, $initializer) {
			$app['dbs.options'] = $app['config']->read('database');
			$initializer();
		});

		$app->register(new DoctrineOrmServiceProvider(), $o = [
			'orm.proxies_dir' => $app['path_cache'] . '/doctrine/proxies',
			'orm.auto_generate_proxies' => true,
			'orm.em.options' => [
				'mappings' => [
					[
						'type' => 'annotation',
						'namespace' => 'Layer\Entity',
						'path' => dirname(__DIR__) . '/Entity'
					]
				]
			]
		]);
/*
		$app['orm.ems.config'] = $app->extend('orm.ems.config', function(\Pimple $configs) use($app) {

			$chain = $app['orm.mapping_driver_chain.locator']('default');
			//die('here');
			$driver = new SimplifiedYamlDriver([
				dirname(__DIR__) . '/Entity' => 'Content'
			]);
			$chain->addDriver($driver, 'Content');

			$configs['default']->setMetadataDriverImpl($chain);

			return $configs;
		});*/
//var_dump($o);
		/*
		$app['db'] = $app->share(function () use ($app) {

			$capsule = new Capsule;

			$connections = $app->config('database.connections') ? : [];

			foreach ($connections as $name => $connection) {
				$capsule->addConnection($connection, $name);
			}

			$capsule->setAsGlobal();
			$capsule->bootEloquent();

			return $capsule;

		});*/


		$app['data.pages'] = $app->share(function() use($app) {
			return new PageType($app);
		});

		$app['data'] = $app->share(function () use ($app) {

			$registry = new DataTypeRegistry($app);
			$registry->load($app['data.pages']);
			return $registry;
		});

		$app['fractal'] = $app->share(function () {
			return new \League\Fractal\Manager();
		});

		$app['fractal.collection'] = function (array $data, $transformer) {
			return new Collection($data, $transformer);
		};

		$app['fractal.item'] = function (array $data, $transformer) {
			return new Item($data, $transformer);
		};

	}

	public function boot(Application $app) {
/*
		//$repo = $app['orm.em']->getRepository('Layer\Page\Page');
var_dump($app['orm.em']->find('Layer\Entity\Content\Page', 1));

		$result = $app['db']->fetchAll('SELECT * FROM content_pages');

var_dump($result);

		die('here');
/*
		foreach ($app['data']->loaded() as $namespace => $tables) {
			foreach ($tables as $table) {
				$name = "{$namespace}/{$table}";
				$app['data.' . $name] = $app['data']->get($name);
			}
		}*/

	}

}