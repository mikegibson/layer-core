<?php

namespace Sentient\Asset;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Filter\CompassFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\UglifyJs2Filter;
use Assetic\FilterManager;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class AssetServiceProvider implements ServiceProviderInterface {

	public function register(Application $app) {

		$app['assetic.options'] = [];

		/**
		 * Asset Factory configuration happens here
		 */
		$app['assetic'] = $app->share(function () use ($app) {
			$app['assetic.options'] = array_replace(array(
				'debug'              => false,
				'formulae_cache_dir' => $app['paths.cache'] . '/assetic'
			), $app['assetic.options']);

			return $app['assetic.factory'];
		});

		$app['paths.cache_assets'] = $app['paths.cache'] . '/assets';

		/**
		 * Asset manager
		 *
		 * @return \Assetic\AssetManager
		 */
		$app['assetic.asset_manager'] = $app->share(function () {
			return new AssetManager();
		});

		$app['assetic.filters.uglifyjs2'] = $app->share(function () {
			return new UglifyJs2Filter();
		});

		$app['assetic.filters.uglifycss'] = $app->share(function () {
			return new UglifyCssFilter();
		});

		$app['assetic.filters.compass'] = $app->share(function () use ($app) {
			$filter = new CompassFilter();
			$filter->setCacheLocation($app['paths.cache'] . '/compass');
			$filter->addLoadPath($app['paths.sentient'] . '/Resource/scss');
			return $filter;
		});

		/**
		 * Filter manager
		 *
		 * @return \Assetic\FilterManager
		 */
		$app['assetic.filter_manager'] = $app->share(function () use ($app) {
			$manager = new FilterManager();
			$manager->set('uglifyjs', $app['assetic.filters.uglifyjs2']);
			$manager->set('uglifycss', $app['assetic.filters.uglifycss']);
			$manager->set('compass', $app['assetic.filters.compass']);
			return $manager;
		});

		/**
		 * Asset writer, writes to the 'assetic.path_to_web' folder
		 *
		 * @return \Assetic\AssetWriter
		 */
		$app['assetic.asset_writer'] = $app->share(function () use ($app) {
			return new AssetWriter($app['paths.cache_assets']);
		});

		/**
		 * Factory
		 *
		 * @return \Assetic\Factory\AssetFactory
		 */
		$app['assetic.factory'] = $app->share(function () use ($app) {
			$factory = new AssetFactory($app['paths.resources'], false);
			$factory->setAssetManager($app['assetic.asset_manager']);
			$factory->setFilterManager($app['assetic.filter_manager']);
			$factory->addPath('sentient', $app['paths.templates'] . '/sentient');
			$factory->addPath('sentient', $app['paths.sentient'] . '/Resource');
			foreach($app->getPluginNames() as $name) {
				$factory->addPath($name, $app['paths.resources'] . '/plugin/' . $name);
				$factory->addPath($name, $app->getPlugin($name)->getPath() . '/Resource');
			}
			return $factory;
		});

		/**
		 * Asset controllers
		 */
		$app['assets.controllers'] = $app->share(function () use ($app) {

			$controllers = $app['controllers_factory'];

			$controllers->match('/{filename}', function (Request $request) use ($app) {
					return new AssetResponse($request->get('asset'), $app['assetic.asset_writer'], $app['paths.cache_assets']);
				})
				->assert('filename', '.+')
				->beforeMatch(function (array $attrs) use ($app) {

					// @todo: A better way than looping through all loaded assets
					foreach ($app['assetic.asset_manager']->getNames() as $name) {
						$asset = $app['assetic.asset_manager']->get($name);
						if ($asset->getTargetPath() === $attrs['filename']) {
							$attrs['asset'] = $asset;
							return $attrs;
						}
					}

					return false;

				})
				->bind('asset');

			return $controllers;

		});

		$app['assets.register_js'] = $app->protect(function($name, $scripts) use($app) {
			$asset = $app['assetic.factory']->createAsset(
				(array) $scripts,
				['uglifyjs'],
				['output' => 'js/' . $name . '.js']
			);
			$app['assetic.asset_manager']->set('js_' . str_replace('/', '_', $name), $asset);
			return $asset;
		});

		$app['assets.register_scss'] = $app->protect(function($name, $stylesheets) use($app) {
			$asset = $app['assetic.factory']->createAsset(
				(array) $stylesheets,
				['compass', 'uglifycss'],
				['output' => 'css/' . $name . '.css']
			);
			$app['assetic.asset_manager']->set('css_' . str_replace('/', '_', $name), $asset);
			return $asset;
		});

		$app['asset_helper'] = $app->share(function () use ($app) {
			return new AssetHelper($app['assetic.asset_manager'], $app['url_generator']);
		});

		$app['twig'] = $app->share(
			$app->extend('twig', function (\Twig_Environment $twig) use ($app) {
				$twig->addExtension(new TwigAssetExtension($app['asset_helper']));
				return $twig;
			})
		);

		$app['filesystem_controllers_factory'] = $app->protect(function($basePath, $routeName = null) use($app) {
			$controllers = $app['controllers_factory'];
			$route = $controllers
				->match('/{filename}', function(Request $request) {
					$file = new FilesystemFile($request->get('path'));
					return new FileResponse($file);
				})
				->beforeMatch(function(array $attrs) use($basePath) {
					if(false !== strpos($attrs['filename'], '..')) {
						return false;
					}
					$attrs['path'] = $basePath . '/' . $attrs['filename'];
					if(!is_file($attrs['path'])) {
						return false;
					}
					return $attrs;
				})
				->assert('filename', '.+')
			;
			if($routeName !== null) {
				$route->bind($routeName);
			}
			return $controllers;
		});

		$app->mount('/assets', $app['assets.controllers']);

	}

	public function boot(Application $app) {

		// Register our filters to use
		if (isset($app['assetic.filters']) && is_callable($app['assetic.filters'])) {
			$app['assetic.filters']($app['assetic.filter_manager']);
		}

		foreach([
			'dropdown'
		] as $script) {
			$app['assets.register_js']($script, '@sentient/js/' . $script . '.js');
		}

	}

}