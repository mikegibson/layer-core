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
			$loader->prependPath($app['paths.templates']);
			$loader->addPath($app['paths.layer'] . '/Template');

			foreach($app['plugins']->loaded() as $name) {
				$plugin = $app['plugins']->get($name);
				foreach ([
					 $app['paths.app'] . '/Template/plugin/' . $name,
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

		$app['twig.default_layout'] = 'layout/app';

		$app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addGlobal('default_layout', $app['twig.default_layout']);
			$twig->addExtension(new FlashExtension());
			$twig->addExtension(new TableExtension());
			$twig->addExtension(new PaginatorExtension());
			$twig->addExtension(new ListExtension());
			return $twig;
		});

		$app['twig.view'] = $app->share(function() use($app) {
			return new View($app['twig']);
		});

	}

}