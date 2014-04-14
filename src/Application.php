<?php

namespace Layer;

use Knp\Provider\ConsoleServiceProvider;
use Layer\Asset\AssetServiceProvider;
use Layer\Config\ConfigServiceProvider;
use Layer\Data\DataProvider;
use Layer\Plugin\PluginServiceProvider;
use Layer\View\Twig\TwigServiceProvider;
use Layer\Utility\ArrayHelper;
use Layer\Utility\Inflector;
use Layer\Utility\StringHelper;
use Layer\View\Html\HtmlHelper;
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
use Silex\Provider\ValidatorServiceProvider;
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

		/**
		 * Set some paths
		 */
		$app['path_root'] = realpath(__DIR__ . '/../../../..');
		$app['path_app'] = $app['path_root'] . '/app';
		$app['path_config'] = $app['path_app'] . '/Config';
		$app['path_resources'] = $app['path_app'] . '/Resource';
		$app['path_templates'] = $app['path_app'] . '/Template';
		$app['path_vendor'] = $app['path_root'] . '/vendor';
		$app['path_public'] = $app['path_root'] . '/public';
		//   $app['path_public_assets'] = $app['path_public'] . '/assets';
		$app['path_storage'] = $app['path_root'] . '/storage';
		$app['path_log'] = $app['path_storage'] . '/log';
		$app['path_tmp'] = $app['path_storage'] . '/tmp';
		$app['path_cache'] = $app['path_tmp'] . '/cache';
		$app['path_session'] = $app['path_tmp'] . '/session';
		$app['path_layer'] = __DIR__;
		$app['path_layer_resources'] = $app['path_layer'] . '/Resource';

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
		$app['html_helper'] = $app->share(function () use ($app) {
			return new HtmlHelper($app);
		});

		/**
		 * Register service providers
		 */
		$app->register(new PluginServiceProvider());
		$app->register(new ServiceControllerServiceProvider());
		$app->register(new UrlGeneratorServiceProvider());
		$app->register(new SessionServiceProvider(), [
			'session.storage.save_path' => $app['path_session']
		]);
		$app->register(new ValidatorServiceProvider());
		$app->register(new HttpFragmentServiceProvider());
		$app->register(new SwiftmailerServiceProvider());
		$app->register(new ConsoleServiceProvider(), [
			'console.name' => 'Layer Console',
			'console.version' => '1.0.0',
			'console.project_directory' => $app['path_root']
		]);
		$app->register(new ConfigServiceProvider());
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
		$app->register(new MonologServiceProvider(), array(
			'monolog.logfile' => $app['path_log'] . '/development.log',
		));
		$app->register(new DataProvider());

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

	}

	/**
	 * Boot the application
	 */
	public function boot() {

		if (!$this->booted) {

			$this['config']->lock();

			$this['debug'] = !!$this->config('debug');

			/**
			 * Set the timezone
			 */
			if ($timezone = $this->config('timezone')) {
				date_default_timezone_set($timezone);
			}

			// @todo Finish config routing
			$mounts = $this->config('routes.mount') ? : [];

			foreach ($mounts as $prefix => $controllers) {
				$this->mount($prefix, $this[$controllers]);
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
		$type = $options['type'];
		unset($options['type']);
		return $this['form.factory']->createNamedBuilder($name, $type, $data, $options);
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

}