<?php

namespace Layer\Config;

use Layer\Config\Driver\JsonConfigDriver;
use Layer\Config\Driver\PhpConfigDriver;
use Layer\Config\Driver\YamlConfigDriver;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;

/**
 * Class ConfigServiceProvider
 *
 * @package Layer\Config
 */
class ConfigServiceProvider implements ServiceProviderInterface {

	/**
	 * @param Application $app
	 */
	public function register(Application $app) {

		$app['config'] = $app->share(function () use ($app) {

			$config = new Configuration($app);

			$config->loadLocator(new FileLocator($app['paths.config']));

			$drivers = [
				new YamlConfigDriver(),
				new PhpConfigDriver(),
				new JsonConfigDriver()
			];

			array_walk($drivers, [$config, 'loadDriver']);

			if(!empty($app['config.autoload'])) {
				$config->load($app['config.autoload']);
			}

			return $config;

		});

	}

	/**
	 * @param Application $app
	 */
	public function boot(Application $app) {

	}

}