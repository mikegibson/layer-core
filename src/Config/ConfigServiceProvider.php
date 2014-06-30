<?php

namespace Sentient\Config;

use Sentient\Config\Driver\JsonConfigDriver;
use Sentient\Config\Driver\PhpConfigDriver;
use Sentient\Config\Driver\YamlConfigDriver;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;

/**
 * Class ConfigServiceProvider
 *
 * @package Sentient\Config
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

		$app['debug'] = $app->share(function() use($app) {
			return $app['config']->read('debug');
		});

	}

	/**
	 * @param Application $app
	 */
	public function boot(Application $app) {

	}

}