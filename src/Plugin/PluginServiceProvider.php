<?php

namespace Layer\Plugin;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class PluginProvider
 *
 * @package Layer\Plugin
 */
class PluginServiceProvider implements ServiceProviderInterface {

	/**
	 * @param Application $app
	 */
	public function register(Application $app) {

		$app['plugins'] = $app->share(function () use ($app) {

			return new PluginCollection($app);

		});

	}

	/**
	 * @param Application $app
	 */
	public function boot(Application $app) {

		$app['plugins']->boot();

	}

}