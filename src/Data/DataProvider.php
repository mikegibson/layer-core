<?php

namespace Layer\Data;

use Illuminate\Database\Capsule\Manager as Capsule;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Silex\Application;
use Silex\ServiceProviderInterface;

class DataProvider implements ServiceProviderInterface {

    public function register(Application $app) {

        $app['db'] = $app->share(function () use ($app) {

            $capsule = new Capsule;

            $connections = $app->config('database.connections') ?: [];

            foreach($connections as $name => $connection) {
                $capsule->addConnection($connection, $name);
            }

            return $capsule;

        });

        $app['data'] = $app->share(function () use ($app) {

            return new DataTypeRegistry($app);

        });

        $app['fractal'] = $app->share(function() {
            return new \League\Fractal\Manager();
        });

        $app['fractal.collection'] = function(array $data, $transformer) {
            return new Collection($data, $transformer);
        };

        $app['fractal.item'] = function(array $data, $transformer) {
            return new Item($data, $transformer);
        };

    }

    public function boot(Application $app) {

        foreach ($app['data']->loaded() as $namespace => $tables) {
            foreach ($tables as $table) {
                $name = "{$namespace}/{$table}";
                $app['data.' . $name] = $app['data']->get($name);
            }
        }

    }

}