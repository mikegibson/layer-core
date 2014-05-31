<?php

namespace Layer\View\Twig;

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

		$app['twig.loader.filesystem'] = $app->share(function () use($app) {

			$loader = new Loader();
			$loader->prependPath($app['path_templates']);
			$loader->addPath($app['path_layer'] . '/Template');

			foreach($app['plugins']->loaded() as $name) {
				$plugin = $app['plugins']->get($name);
				foreach ([
					 $app['path_app'] . '/Template/plugin/' . $name,
					 $plugin->getPath() . '/Template'
				 ] as $path) {
					if (is_dir($path)) {
						$loader->addPath($path, $name);
					}
				}
			}

			return $loader;
		});

		$app['twig.form.templates'] = ['form/default.twig'];

		$app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addGlobal('app_name', $app['config']->read('name'));
			$twig->addGlobal('app_charset', strtolower($app['charset']));
			$twig->addExtension(new FlashExtension());
			$twig->addExtension(new TableExtension());
			$twig->addExtension(new PaginatorExtension());
			$twig->addExtension(new ListExtension());
			return $twig;
		}));

		$app['twig.view'] = $app->share(function() use($app) {
			return new View($app['twig']);
		});

	}

}