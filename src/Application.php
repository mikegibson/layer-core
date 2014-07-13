<?php

namespace Sentient;

use Knp\Provider\ConsoleServiceProvider;
use Sentient\Action\ActionDispatcher;
use Sentient\Action\ActionInterface;
use Sentient\Action\SimpleAction;
use Sentient\Asset\AssetServiceProvider;
use Sentient\Cms\CmsPlugin;
use Sentient\Data\DataProvider;
use Sentient\Data\ValidatorServiceProvider;
use Sentient\Form\FormServiceProvider;
use Sentient\Media\MediaPlugin;
use Sentient\Node\ControllerNode;
use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\ControllerNodeListNode;
use Sentient\Node\ListNode;
use Sentient\Plugin\PluginInterface;
use Sentient\Route\UrlMatcher;
use Sentient\Users\UsersPlugin;
use Sentient\Utility\StringHelper;
use Sentient\View\Twig\TwigServiceProvider;
use Sentient\Utility\ArrayHelper;
use Sentient\Utility\Inflector;
use Silex\Application as Silex;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\ServiceProviderInterface;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class Application
 *
 * @package Sentient
 */
class Application extends Silex {

	private $plugins = [];

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		$app = $this;

		foreach($this->getPaths() as $key => $path) {
			$app['paths.' . $key] = $path;
		}

		$app['class_loader'] = $app->share(function() use($app) {
			return require $app['paths.vendor'] . '/autoload.php';
		});

		/**
		 * Share helpers and utility classes
		 */
		$app['inflector'] = $app->share(function () use ($app) {
			return new Inflector($app);
		});
		$app['array_helper'] = $app->share(function () use ($app) {
			return new ArrayHelper($app);
		});
		$app['string_helper'] = $app->share(function() use($app) {
			return new StringHelper($app['charset']);
		});
		$app['property_accessor'] = $app->share(function() use($app) {
			return new PropertyAccessor();
		});

		$app['security.firewalls'] = $app->share(function() {
			return [];
		});

		$app['route_class'] = 'Sentient\\Route\\Route';

		$app->registerErrorHandlers();

		$app['url_matcher'] = $app->share(function() use($app) {
			return new UrlMatcher($app['routes'], $app['request_context']);
		});

		$app['actions.dispatcher'] = $app->share(function() use($app) {
			return new ActionDispatcher($app['dispatcher'], $app['twig.view']);
		});

		$app['actions.dispatch'] = $app->protect(function(ActionInterface $action) use($app) {
			return function(Request $request) use($app, $action) {
				return $app['actions.dispatcher']->dispatch($action, $request);
			};
		});

		$app['nodes.matcher'] = $app->protect(
			function(ControllerNodeInterface $node, $key = 'node') {
				return function(array $attrs) use($node, $key) {
					$path = trim($attrs[$key], '/');
					$parts = $path === '' ? [] : explode('/', $path);
					while(!empty($parts)) {
						$part = array_shift($parts);
						try {
							$node = $node->getChild($part);
						} catch(\InvalidArgumentException $e) {
							return false;
						}
					}
					if(!$node->isAccessible()) {
						return false;
					}
					$attrs[$key] = $node;
					return $attrs;
				};
			}
		);

		$app['nodes.dispatcher'] = $app->protect(function($key = 'node', $routeName = null) use($app) {
			return function(Request $request) use($app, $key, $routeName) {
				$node = $request->get($key);
				if(!$node instanceof ControllerNodeInterface || !$node->isAccessible()) {
					$app->abort(404);
				}
				return $app['actions.dispatcher']->dispatch($node, $request, $routeName);
			};
		});

		$app['nodes.controllers_factory'] = $app->protect(
			function(ControllerNodeInterface $rootNode, $routeName = null, $key = 'node') use($app) {
				$controllers = $app['controllers_factory'];
				$route = $controllers->match('/{' . $key . '}', $app['nodes.dispatcher']($key, $routeName))
					->value($key, '')
					->assert($key, '.*')
					->beforeMatch($app['nodes.matcher']($rootNode, $key));
				if($routeName !== null) {
					$route->bind($routeName);
				}
				$rootNode->connectControllers($controllers);
				return $controllers;
			}
		);

		$app['name'] = 'Sentient';

		$app['timezone'] = 'Europe/London';

		$app['home_template'] = 'view/home';

		$app['home_action'] = $app->share(function() use($app) {
			return new SimpleAction('home', 'Home', $app['home_template']);
		});

		$app['home_node'] = $app->share(function() use($app) {
			return new ControllerNode($app['home_action']);
		});

		$app['home_controllers'] = $app->share(function() use($app) {
			return $app['nodes.controllers_factory']($app['home_node'], 'app');
		});

		$app['home_list_node'] = $app->share(function() use($app) {
			return new ControllerNodeListNode($app['home_node'], 'app', $app['url_generator']);
		});

		$app['navigation'] = $app->share(function() use($app) {
			$rootNode = new ListNode();
			$homeNode = new ControllerNodeListNode($app['home_node'], 'app', $app['url_generator'], $rootNode, false);
			$rootNode->registerChild($homeNode);
			$rootNode->adoptChildren($app['home_list_node']);
			return $rootNode;
		});

		$app
			->register(new ServiceControllerServiceProvider())
			->register(new UrlGeneratorServiceProvider())
			->register(new SessionServiceProvider(), [
				'session.storage.save_path' => $app['paths.session']
			])
			->register(new HttpFragmentServiceProvider())
			->register(new SwiftmailerServiceProvider())
			->register(new ConsoleServiceProvider(), [
				'console.name' => 'Sentient Console',
				'console.version' => '1.0.0',
				'console.project_directory' => $app['paths.root']
			])
			->register(new TwigServiceProvider(), [
				'twig.options' => [
					'cache' => $app['debug'] ? false : $app['paths.cache'] . '/twig',
					'auto_reload' => true
				]
			])
			->register(new AssetServiceProvider())
			->register(new TranslationServiceProvider(), [
				'locale_fallbacks' => ['en'],
			])
			->register(new FormServiceProvider())
			->register(new DataProvider())
			->register(new ValidatorServiceProvider())
			->register(new CmsPlugin())
			->register(new UsersPlugin())
			->register(new MediaPlugin())
		;

		if(!isset($app['environment'])) {
			$app['environment'] = getenv('ENVIRONMENT');
		}

		if($app['environment']) {
			$configPath = $app['paths.config'] . '/' . $app['environment'] . '.php';
			if(is_file($configPath)) {
				include $configPath;
			}
		}

	}

	/**
	 * Boot the application
	 */
	public function boot() {

		if (!$this->booted) {

			if(!isset($this['debug'])) {
				$this['debug'] = false;
			}

			if(!isset($this['salt'])) {
				$this['salt'] = md5(__DIR__);
			}

			date_default_timezone_set($this['timezone']);

			$this->initialize();
			$this->initializeSecurity();
			$this->connectRoutes();
			if($this['debug']) {
				$this->initializeProfiler();
			}

		}

		parent::boot();
	}

	protected function initialize() {}

	/**
	 * @param ServiceProviderInterface $serviceProvider
	 * @param array $values
	 * @return Application
	 * @throws \RuntimeException If plugin is already loaded
	 */
	public function register(ServiceProviderInterface $serviceProvider, array $values = []) {
		if($serviceProvider instanceof PluginInterface) {
			$name = $serviceProvider->getName();
			if($this->hasPlugin($name)) {
				throw new \RuntimeException(sprintf('Plugin %s is already loaded.', $name));
			}
			$this->plugins[$name] = $serviceProvider;
		}
		return parent::register($serviceProvider, $values);
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasPlugin($name) {
		return isset($this->plugins[$name]);
	}

	/**
	 * @param $name
	 * @return \Sentient\Plugin\PluginInterface
	 * @throws \InvalidArgumentException
	 */
	public function getPlugin($name) {
		if(!$this->hasPlugin($name)) {
			throw new \InvalidArgumentException(sprintf('Plugin %s is not loaded', $name));
		}
		return $this->plugins[$name];
	}

	/**
	 * @return array
	 */
	public function getPluginNames() {
		return array_keys($this->plugins);
	}

	/**
	 * @param $name
	 * @param null $data
	 * @param array $options
	 * @return \Symfony\Component\Form\FormBuilderInterface
	 */
	public function form($name, $data = null, array $options = []) {
		$options = array_merge(['type' => 'form'], $options);
		if($name instanceof FormTypeInterface) {
			$type = $name;
			$name = $type->getName();
		} else {
			$type = $options['type'];
		}
		unset($options['type']);
		return $this['form.factory']->createNamedBuilder($name, $type, $data, $options);
	}

	public function addFlash($key, $message) {
		return $this['session']->getFlashBag()->add($key, $message);
	}

	/**
	 * @param Request $request
	 * @param int $type
	 * @param bool $catch
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true) {
		if($type === HttpKernelInterface::MASTER_REQUEST) {
			$request->enableHttpMethodParameterOverride();
		}
		return parent::handle($request, $type, $catch);
	}

	public function isCli() {
		return (php_sapi_name() === 'cli');
	}

	protected function connectRoutes() {

		/**
		 * Mount the base app controller
		 */
		$this->mount('/', $this['home_controllers']);

	}

	protected function getPaths() {

		$root = realpath(__DIR__ . '/../../../..');
		$app = $root . '/app';
		$config = $app . '/Config';
		$resources = $app . '/Resource';
		$templates = $app . '/Template';
		$vendor = $root . '/vendor';
		$public = $root . '/public';
		$storage = $root . '/storage';
		$log = $storage . '/log';
		$tmp = $storage . '/tmp';
		$cache = $tmp . '/cache';
		$session = $tmp . '/session';
		$sentient = __DIR__;
		return compact('root', 'app', 'config', 'resources', 'templates', 'vendor',
			'public', 'storage', 'log', 'tmp', 'cache', 'session', 'sentient');
	}

	protected function registerErrorHandlers() {

		$app = $this;

		/**
		 * Turn on some debugging features if in debug mode
		 */
		if ($app['debug']) {

			error_reporting(-1);

			ErrorHandler::register();

			// CLI - display errors only if they're not already logged to STDERR
			if (!$app->isCli()) {
				ExceptionHandler::register();
			} elseif (!ini_get('log_errors') || ini_get('error_log')) {
				ini_set('display_errors', 1);
			}

		}

		$app->error(function (\Exception $error) use($app) {
			return $app['twig']->render('view/error.twig', compact('error'));
		});

	}

	protected function initializeSecurity() {
		if(!empty($this['security.firewalls'])) {
			$hierarchy = $this['security.role_hierarchy'];
			$this->register(new SecurityServiceProvider());
			$this['security.role_hierarchy'] = $hierarchy;
			$this->register(new RememberMeServiceProvider());
		}
	}

	protected function initializeProfiler() {
		$this->register(new WebProfilerServiceProvider(), [
			'profiler.cache_dir' => $this['paths.cache'] . '/profiler',
			'profiler.mount_prefix' => '/_profiler'
		]);
	}

}