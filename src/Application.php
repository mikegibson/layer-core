<?php

namespace Layer;

use Knp\Provider\ConsoleServiceProvider;
use Layer\Action\ActionDispatcher;
use Layer\Asset\AssetServiceProvider;
use Layer\Config\ConfigServiceProvider;
use Layer\Config\Configuration;
use Layer\Data\DataProvider;
use Layer\Node\ControllerNodeInterface;
use Layer\Plugin\PluginServiceProvider;
use Layer\Route\UrlMatcher;
use Layer\View\Twig\TwigServiceProvider;
use Layer\Utility\ArrayHelper;
use Layer\Utility\Inflector;
use Layer\Utility\StringHelper;
use Silex\Application\MonologTrait;
use Silex\Application\SecurityTrait;
use Silex\Application\SwiftmailerTrait;
use Silex\Application\TranslationTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Application
 *
 * @package Layer
 */
class Application extends \Silex\Application {

	use MonologTrait, SecurityTrait, SwiftmailerTrait, TranslationTrait, TwigTrait, UrlGeneratorTrait;

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		$app = $this;

		$this->setPaths();

		$app['class_loader'] = $app->share(function() use($app) {
			return require $app['path_vendor'] . '/autoload.php';
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
		$app['string_helper'] = $app->share(function () use ($app) {
			return new StringHelper($app);
		});

		$app->register(new ConfigServiceProvider());

		/**
		 * Load config files
		 */
		$app->extend('config', function(Configuration $config) use($app) {
			$config->load($this->getConfigAutoload());
			return $config;
		});

		$app['debug'] = $app->protect(function() use($app) {
			return $app['config']->read('debug');
		});

		$this->registerErrorHandlers();

		/**
		 * Register service providers
		 */
		$app->register(new PluginServiceProvider());
		$app->register(new ServiceControllerServiceProvider());
		$app->register(new UrlGeneratorServiceProvider());
		$app->register(new SessionServiceProvider(), [
			'session.storage.save_path' => $app['path_session']
		]);
		$app->register(new HttpFragmentServiceProvider());
		$app->register(new SwiftmailerServiceProvider());
		$app->register(new ConsoleServiceProvider(), [
			'console.name' => 'Layer Console',
			'console.version' => '1.0.0',
			'console.project_directory' => $app['path_root']
		]);
		$app->register(new TwigServiceProvider(), [
			'twig.options' => [
				'cache' => $app['path_cache'] . '/twig',
				'auto_reload' => true
			]
		]);
		//   $app['assetic.path_to_source'] = $app['path_resources'];
		// $app['assetic.path_to_web'] = $app['path_public_assets'];
		$app->register(new AssetServiceProvider());
		$app->register(new TranslationServiceProvider(), [
			'locale_fallbacks' => array('en'),
		]);
		$app->register(new FormServiceProvider());
		// @todo set $app['form.secret'] to something better
		$app->register(new MonologServiceProvider(), array(
			'monolog.logfile' => $app['path_log'] . '/development.log',
		));
		$app->register(new DataProvider());

		$app['route_class'] = 'Layer\\Route\\Route';

		$app['url_matcher'] = $app->share(function() use($app) {
			return new UrlMatcher($app['routes'], $app['request_context']);
		});

		$app['actions.dispatcher'] = $app->share(function() use($app) {
			return new ActionDispatcher($app['twig.view']);
		});

		$app['assets.js.modernizr'] = $app->share(function () use ($app) {
			$asset = $app['assetic.factory']->createAsset([
				'@layer/js/modernizr.js'
			], [
				'uglifyjs'
			], [
				'output' => 'js/modernizr.js'
			]);
			return $asset;
		});

		$app['nodes.matcher'] = $app->protect(function(ControllerNodeInterface $node, $key = 'node') {
			return function(array $attrs) use($node, $key) {
				try {
					$nodePath = trim($attrs[$key], '/');
					if($nodePath !== '') {
						$node = $node->getDescendent($nodePath);;
					}
					$attrs[$key] = $node;
				} catch(\InvalidArgumentException $e) {
					return false;
				}
				return $attrs;
			};
		});

		$app['nodes.dispatcher'] = $app->protect(function($key = 'node') use($app) {
			return function(Request $request) use($app, $key) {
				return $app['actions.dispatcher']->dispatch($request->get($key), $request);
			};
		});

		$app['nodes.controllers_factory'] = $app->protect(function(ControllerNodeInterface $rootNode, $key = 'node') use($app) {
			$controllers = $app['controllers_factory'];
			$controllers->match('/{' . $key . '}', $app['nodes.dispatcher']($key))
				->assert($key, '.*')
				->beforeMatch($app['nodes.matcher']($rootNode, $key))
				->bind($rootNode->getRouteName());
			return $controllers;
		});

	}

	/**
	 * Boot the application
	 */
	public function boot() {

		if (!$this->booted) {

			/**
			 * Set the timezone
			 */
			if ($timezone = $this->config('timezone')) {
				date_default_timezone_set($timezone);
			}

			$this['assetic.asset_manager']->set('js_modernizr', $this['assets.js.modernizr']);

		}

		parent::boot();
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

	protected function setPaths() {
		$this['path_root'] = realpath(__DIR__ . '/../../../..');
		$this['path_app'] = $this['path_root'] . '/app';
		$this['path_config'] = $this['path_app'] . '/Config';
		$this['path_resources'] = $this['path_app'] . '/Resource';
		$this['path_templates'] = $this['path_app'] . '/Template';
		$this['path_vendor'] = $this['path_root'] . '/vendor';
		$this['path_public'] = $this['path_root'] . '/public';
		//   $this['path_public_assets'] = $this['path_public'] . '/assets';
		$this['path_storage'] = $this['path_root'] . '/storage';
		$this['path_log'] = $this['path_storage'] . '/log';
		$this['path_tmp'] = $this['path_storage'] . '/tmp';
		$this['path_cache'] = $this['path_tmp'] . '/cache';
		$this['path_session'] = $this['path_tmp'] . '/session';
		$this['path_layer'] = __DIR__;
		$this['path_layer_resources'] = $this['path_layer'] . '/Resource';
	}

	protected function getConfigAutoload() {
		return [
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

	protected function registerErrorHandlers() {

		/**
		 * Turn on some debugging features if in debug mode
		 */
		if ($this['debug']) {

			error_reporting(-1);

			\Symfony\Component\Debug\ErrorHandler::register();

			if ('cli' !== php_sapi_name()) {
				\Symfony\Component\Debug\ExceptionHandler::register();
				// CLI - display errors only if they're not already logged to STDERR
			} elseif (!ini_get('log_errors') || ini_get('error_log')) {
				ini_set('display_errors', 1);
			}

			$this->error(function (\Exception $e) {
				return new \Symfony\Component\HttpFoundation\Response(
					nl2br($e->getMessage() . PHP_EOL . $e->getTraceAsString())
				);
			});

		}

	}

}