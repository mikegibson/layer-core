<?php

namespace Layer;

use Knp\Provider\ConsoleServiceProvider;
use Layer\Action\ActionDispatcher;
use Layer\Action\SimpleAction;
use Layer\Asset\AssetServiceProvider;
use Layer\Cms\CmsPlugin;
use Layer\Config\ConfigServiceProvider;
use Layer\Data\DataProvider;
use Layer\Form\FormServiceProvider;
use Layer\Media\MediaPlugin;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;
use Layer\Node\ControllerNodeListNode;
use Layer\Node\ListNode;
use Layer\Plugin\PluginInterface;
use Layer\Route\UrlMatcher;
use Layer\Users\UsersPlugin;
use Layer\Utility\StringHelper;
use Layer\View\Twig\TwigServiceProvider;
use Layer\Utility\ArrayHelper;
use Layer\Utility\Inflector;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
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
 * @package Layer
 */
class Application extends \Silex\Application {

	private $plugins = [];

	/**
	 * Constructor
	 */
	public final function __construct() {

		parent::__construct();

		$app = $this;

		foreach($this->getPaths() as $key => $path) {
			$app['paths.' . $key] = $path;
		}

		if(!isset($app['config.autoload'])) {
			$app['config.autoload'] = [
				'app' => [
					'nest' => false
				],
				'database',
				'local' => [
					'nest' => false,
					'ignoreMissing' => true
				]
			];
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

		$app->register(new ConfigServiceProvider());

		$app['route_class'] = 'Layer\\Route\\Route';

		$app->registerErrorHandlers();

		$app['url_matcher'] = $app->share(function() use($app) {
			return new UrlMatcher($app['routes'], $app['request_context']);
		});

		$app['actions.dispatcher'] = $app->share(function() use($app) {
			return new ActionDispatcher($app['dispatcher'], $app['twig.view']);
		});

		$app['nodes.matcher'] = $app->protect(
			function(ControllerNodeInterface $node, $key = 'node', $rejectNotFound = true) {
				return function(array $attrs) use($node, $key, $rejectNotFound) {
					try {
						$nodePath = trim($attrs[$key], '/');
						if($nodePath !== '') {
							$node = $node->getDescendent($nodePath);;
						}
						if($rejectNotFound && !$node->isAccessible()) {
							return false;
						}
						$attrs[$key] = $node;
					} catch(\InvalidArgumentException $e) {
						if($rejectNotFound) {
							return false;
						}
					}
					return $attrs;
				};
			}
		);

		$app['nodes.dispatcher'] = $app->protect(function($key = 'node') use($app) {
			return function(Request $request) use($app, $key) {
				$node = $request->get($key);
				if(!$node instanceof ControllerNodeInterface || !$node->isAccessible()) {
					$app->abort(404);
				}
				return $app['actions.dispatcher']->dispatch($node, $request);
			};
		});

		$app['nodes.controllers_factory'] = $app->protect(
			function(ControllerNodeInterface $rootNode, $key = 'node', $rejectNotFound = true) use($app) {
				$controllers = $app['controllers_factory'];
				$controllers->match('/{' . $key . '}', $app['nodes.dispatcher']($key))
					->value($key, '')
					->assert($key, '.*')
					->beforeMatch($app['nodes.matcher']($rootNode, $key, $rejectNotFound))
					->bind($rootNode->getRouteName());
				return $controllers;
			}
		);

		$app['app.home_template'] = 'view/home';

		$app['app.home_action'] = $app->share(function() use($app) {
			return new SimpleAction('home', 'Home', $app['app.home_template']);
		});

		$app['app.home_node'] = $app->share(function() use($app) {
			return new ControllerNode('app', $app['app.home_action']);
		});

		$app['app.controllers'] = $app->share(function() use($app) {
			return $app['nodes.controllers_factory']($app['app.home_node']);
		});

		$app['app.home_list_node'] = $app->share(function() use($app) {
			return new ControllerNodeListNode($app['app.home_node'], $app['url_generator']);
		});

		$app['app.navigation'] = $app->share(function() use($app) {
			$rootNode = new ListNode();
			$homeNode = new ControllerNodeListNode($app['app.home_node'], $app['url_generator'], $rootNode, false);
			$rootNode->registerChildNode($homeNode);
			$rootNode->adoptChildNodes($app['app.home_list_node']);
			return $rootNode;
		});

		$this->registerServiceProviders();

	}

	/**
	 * Boot the application
	 */
	public function boot() {

		if (!$this->booted) {

			$this->setTimezone();
			$this->initialize();
			$this->initializeSecurity();
			$this->connectRoutes();

		}

		parent::boot();
	}

	/**
	 * @param ServiceProviderInterface $serviceProvider
	 * @param array $values
	 * @return \Silex\Application
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
	 * @return \Layer\Plugin\PluginInterface
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
	 * Read a configuration value
	 *
	 * @param $key
	 * @return mixed
	 */
	public function config($key) {
		return $this['config']->read($key);
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

	protected function initialize() {}

	protected function connectRoutes() {}

	protected function getPaths() {

		$root = realpath(__DIR__ . '/../../../..');
		$app = $root . '/app';
		$config = $app . '/Config';
		$resources = $app . '/Resource';
		$templates = $app . '/Template';
		$vendor = $root . '/vendor';
		$public = $root . '/public';
		//   $this['paths.public_assets'] = $this['paths.public'] . '/assets';
		$storage = $root . '/storage';
		$log = $storage . '/log';
		$tmp = $storage . '/tmp';
		$cache = $tmp . '/cache';
		$session = $tmp . '/session';
		$layer = __DIR__;
		return compact('root', 'app', 'config', 'resources', 'templates', 'vendor',
			'public', 'storage', 'log', 'tmp', 'cache', 'session', 'layer');
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

	protected function registerServiceProviders() {
		$this
			->register(new ServiceControllerServiceProvider())
			->register(new UrlGeneratorServiceProvider())
			->register(new SessionServiceProvider(), [
				'session.storage.save_path' => $this['paths.session']
			])
			->register(new HttpFragmentServiceProvider())
			->register(new SwiftmailerServiceProvider())
			->register(new ConsoleServiceProvider(), [
				'console.name' => 'Layer Console',
				'console.version' => '1.0.0',
				'console.project_directory' => $this['paths.root']
			])
			->register(new TwigServiceProvider(), [
				'twig.options' => [
					'cache' => $this['debug'] ? false : $this['paths.cache'] . '/twig',
					'auto_reload' => true
				]
			])
			->register(new AssetServiceProvider())
			->register(new TranslationServiceProvider(), [
				'locale_fallbacks' => ['en'],
			])
			->register(new FormServiceProvider())
			->register(new ValidatorServiceProvider())
			->register(new DataProvider())
			->register(new CmsPlugin())
			->register(new UsersPlugin())
			->register(new MediaPlugin())
		;
	}

	protected function setTimezone() {
		if ($timezone = $this['config']->read('timezone')) {
			date_default_timezone_set($timezone);
		} else {
			throw new \RuntimeException('No timezone has been configured.');
		}
	}

	protected function initializeSecurity() {
		if(!empty($this['security.firewalls'])) {
			$this->register(new SecurityServiceProvider());
			$this->register(new RememberMeServiceProvider());
		}
	}

}