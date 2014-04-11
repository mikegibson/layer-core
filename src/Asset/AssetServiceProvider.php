<?php

namespace Layer\Asset;

use Assetic\AssetManager;
use Assetic\AssetWriter;
use Assetic\Filter\CompassFilter;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\UglifyJs2Filter;
use Assetic\FilterManager;
use Silex\Application;
use Silex\ServiceProviderInterface;

class AssetServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {

        $app['assetic.options'] = [];

        /**
         * Asset Factory configuration happens here
         */
        $app['assetic'] = $app->share(function () use ($app) {
            $app['assetic.options'] = array_replace(array(
             //   'debug'              => $debug,
                'formulae_cache_dir' => $app['path_cache'] . '/assetic'
            ), $app['assetic.options']);

            return $app['assetic.factory'];
        });

        $app['path_cache_assets'] = $app['path_cache'] . '/assets';
        $app['path_cache_assets_debug'] = $app['path_cache'] . '/assets_debug';

        /**
         * Asset manager
         *
         * @return Assetic\AssetManager
         */
        $app['assetic.asset_manager'] = $app->share(function() {
            return new AssetManager();
        });

        $app['assetic.filters.jsmin'] = $app->share(function() {
            return new JSMinFilter();
        });

        $app['assetic.filters.uglifyjs2'] = $app->share(function() {
            return new UglifyJs2Filter();
        });

        $app['assetic.filters.uglifycss'] = $app->share(function() {
            return new UglifyCssFilter();
        });

        $app['assetic.filters.compass'] = $app->share(function() use ($app) {
            $filter = new CompassFilter();
            $filter->setCacheLocation($app['path_cache'] . '/compass');
            return $filter;
        });

        /**
         * Filter manager
         *
         * @return Assetic\FilterManager
         */
        $app['assetic.filter_manager'] = $app->share(function () use ($app) {
            $manager = new FilterManager();
            $manager->set('jsmin', $app['assetic.filters.jsmin']);
            $manager->set('uglifyjs', $app['assetic.filters.uglifyjs2']);
            $manager->set('uglifycss', $app['assetic.filters.uglifycss']);
            $manager->set('compass', $app['assetic.filters.compass']);
            return $manager;
        });

        /**
         * Asset writer, writes to the 'assetic.path_to_web' folder
         *
         * @return Assetic\AssetWriter
         */
        $app['assetic.asset_writer'] = $app->share(function () use ($app) {
            return new AssetWriter($app['path_cache_assets']);
        });

        /**
         * Factory
         *
         * @return Assetic\Factory\AssetFactory
         */
        $app['assetic.factory'] = $app->share(function () use ($app) {
            $factory = new AssetFactory($app);
            $factory->setAssetManager($app['assetic.asset_manager']);
            $factory->setFilterManager($app['assetic.filter_manager']);
            return $factory;
        });

        /**
         * Asset controllers
         */
        $app['assets.controllers'] = $app->share(function () use ($app) {

            $controllers = $app['controllers_factory'];

            $controllers->get('/{asset}', function($asset) use($app) {

                return new AssetResponse($app, $asset);

            })
            ->assert('asset', '.+')
            ->convert('asset', function($file) use($app) {

           /*     if(0 === strpos($file, 'debug/')) {
                    $target = substr($file, 6);
                    $debug = true;
                } else {*/
                    $target = $file;
                    $debug = false;
                //}

                $app['assetic.factory']->setDebug($debug);

                // @todo: A better way than looping through all loaded assets
                foreach ($app['assetic.asset_manager']->getNames() as $name) {
                    $asset = $app['assetic.asset_manager']->get($name);
                    if($asset->getTargetPath() === $target) {
                        if($debug) {
                            $asset->setTargetPath('debug/' . $target);
                        }
                        return $asset;
                    }
                }

                $app->abort(404);

            });

            return $controllers;

        });

        $app->mount('/assets', $app['assets.controllers']);

    }

    public function boot(Application $app) {

        // Register our filters to use
        if (isset($app['assetic.filters']) && is_callable($app['assetic.filters'])) {
            $app['assetic.filters']($app['assetic.filter_manager']);
        }

    }

}