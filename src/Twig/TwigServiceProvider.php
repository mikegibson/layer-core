<?php

namespace Layer\Twig;

use Layer\Twig\TwigLoader;
use Layer\Twig\TwigHtmlExtension;
use Layer\Twig\TwigTableExtension;
use Silex\Application;

/**
 * Class TwigServiceProvider
 *
 * @package Layer\Twig
 */
class TwigServiceProvider extends \Silex\Provider\TwigServiceProvider {

	/**
	 * @param Application $app
	 */
	public function register(Application $app) {

		parent::register($app);

		$app['twig.loader.filesystem'] = $app->share(function () {
			return new TwigLoader();
		});

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
				$twig->addExtension(new TwigHtmlExtension($app['html_helper']));
				$twig->addExtension(new TwigTableExtension($app['table_helper']));
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