<?php

namespace Layer\View\Twig;

use Layer\View\Table\TwigTableExtension;
use Silex\Application;

/**
 * Class TwigServiceProvider
 *
 * @package Layer\View\Twig
 */
class TwigServiceProvider extends \Silex\Provider\TwigServiceProvider {

	/**
	 * @param Application $app
	 */
	public function register(Application $app) {

		parent::register($app);

		$app['twig.loader.filesystem'] = $app->share(function () {
			return new Loader();
		});

		$app['twig.form.templates'] = ['form/default.twig'];

		$app['twig.table.template'] = 'table/default.twig';
		$app['twig.table.extension'] = $app->share(function() use($app) {
			return new TwigTableExtension();
		});

		$app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addExtension($app['twig.table.extension']);
			return $twig;
		}));


	}

	/**
	 * @param Application $app
	 */
	public function boot(Application $app) {

		parent::boot($app);

		$app['twig'] = $app->share(
			$app->extend('twig', function (\Twig_Environment $twig) use ($app) {
				$twig->addGlobal('app_name', $app['config']->read('name'));
				$twig->addGlobal('app_charset', strtolower($app['charset']));
				$twig->addExtension(new HtmlExtension($app['html_helper']));
				$twig->addExtension(new FlashExtension());
				$twig->addExtension($app['twig.table.extension']);
				return $twig;
			})
		);

		$app['twig.loader.filesystem']->prependPath($app['path_templates']);
		$app['twig.loader.filesystem']->addPath($app['path_layer'] . '/Template');

		foreach ($app['plugins']->loaded() as $name) {
			$plugin = $app['plugins']->get($name);
			foreach ([
						 $app['path_app'] . '/Template/plugin/' . $name,
						 $plugin->getPath() . '/Template'
					 ] as $path) {
				if (is_dir($path)) {
					$app['twig.loader.filesystem']->addPath($path, $name);
				}
			}
		}

	}

}