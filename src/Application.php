<?php

namespace Layer;

use Knp\Provider\ConsoleServiceProvider;
use Layer\Action\ActionDispatcher;
use Layer\Action\SimpleAction;
use Layer\Asset\AssetServiceProvider;
use Layer\Config\ConfigServiceProvider;
use Layer\Data\DataProvider;
use Layer\Form\FormServiceProvider;
use Layer\Media\MediaProvider;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;
use Layer\Plugin\PluginInterface;
use Layer\Route\UrlMatcher;
use Layer\View\Twig\TwigServiceProvider;
use Layer\Utility\ArrayHelper;
use Layer\Utility\Inflector;
use Silex\Application\MonologTrait;
use Silex\Application\SecurityTrait;
use Silex\Application\SwiftmailerTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class Application
 *
 * @package Layer
 */
class Application extends \Silex\Application {

	use MonologTrait, SecurityTrait, SwiftmailerTrait, TranslationTrait, TwigTrait, UrlGeneratorTrait;

	public $assets = [];

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

		$app['debug'] = $app->protect(function() use($app) {
			return $app['config']->read('debug');
		});

		$app['security.firewalls'] = $app->share(function() {
			return [];
		});

		$this->registerServiceProviders();

		$this->registerErrorHandlers();


		/**
		 * Share helpers and utility classes
		 */
		$app['inflector'] = $app->share(function () use ($app) {
			return new Inflector($app);
		});
		$app['array_helper'] = $app->share(function () use ($app) {
			return new ArrayHelper($app);
		});
		$app['property_accessor'] = $app->share(function() use($app) {
			return new PropertyAccessor();
		});

		$app['route_class'] = 'Layer\\Route\\Route';

		$app['url_matcher'] = $app->share(function() use($app) {
			return new UrlMatcher($app['routes'], $app['request_context']);
		});

		$app['actions.dispatcher'] = $app->share(function() use($app) {
			return new ActionDispatcher($app['dispatcher'], $app['twig.view']);
		});

		$app[$this->assets['js_modernizr'] = 'assets.js.modernizr'] = $app->share(function () use ($app) {
			$asset = $app['assetic.factory']->createAsset([
				'@layer/js/modernizr.js'
			], [
				'uglifyjs'
			], [
				'output' => 'js/modernizr.js'
			]);
			return $asset;
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

	}

	/**
	 * Boot the application
	 */
	public function boot() {

		if (!$this->booted) {

			$this->setTimezone();
			$this->initializeSecurity();
			$this->mountControllers();

			foreach($this->assets as $name => $appKey) {
				$this['assetic.asset_manager']->set($name, $this[$appKey]);
			}

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

		/**
		 * Turn on some debugging features if in debug mode
		 */
		if ($this['debug']) {

			error_reporting(-1);

			ErrorHandler::register();

			// CLI - display errors only if they're not already logged to STDERR
			if (!$this->isCli()) {
				ExceptionHandler::register();
			} elseif (!ini_get('log_errors') || ini_get('error_log')) {
				ini_set('display_errors', 1);
			}

			$this->error(function (\Exception $e) {
				return new Response(
					nl2br($e->getMessage() . PHP_EOL . $e->getTraceAsString())
				);
			});

		}

	}

	protected function registerServiceProviders() {
		$this->register(new ConfigServiceProvider());
		$this->register(new ServiceControllerServiceProvider());
		$this->register(new UrlGeneratorServiceProvider());
		$this->register(new SessionServiceProvider(), [
			'session.storage.save_path' => $this['paths.session']
		]);

		$this->register(new HttpFragmentServiceProvider());
		$this->register(new SwiftmailerServiceProvider());
		$this->register(new ConsoleServiceProvider(), [
			'console.name' => 'Layer Console',
			'console.version' => '1.0.0',
			'console.project_directory' => $this['paths.root']
		]);
		$this->register(new TwigServiceProvider(), [
			'twig.options' => [
				'cache' => $this['paths.cache'] . '/twig',
				'auto_reload' => true
			]
		]);
		$this->register(new AssetServiceProvider());
		$this->register(new TranslationServiceProvider(), [
			'locale_fallbacks' => ['en'],
		]);
		$this->register(new FormServiceProvider());
		// @todo set $this->app['form.secret'] to something better
		$this->register(new MonologServiceProvider(), [
			'monolog.logfile' => $this['paths.log'] . '/development.log',
		]);
		$this->register(new ValidatorServiceProvider());
		$this->register(new DataProvider());
		$this->register(new MediaProvider());
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

	protected function mountControllers() {
		$this->mount('/', $this['app.controllers']);
	}

}