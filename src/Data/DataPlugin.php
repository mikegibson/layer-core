<?php

namespace Layer\Data;

use Illuminate\Database\Capsule\Manager as Capsule;
use Layer\Plugin\Plugin;

class DataPlugin extends Plugin {

    protected $name = 'data';

    public function register() {

        $app = $this->app;

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

    }

    public function boot() {

        $app = $this->app;

        foreach ($app['data']->loaded() as $namespace => $tables) {
            foreach ($tables as $table) {
                $name = "{$namespace}/{$table}";
                $app['data.' . $name] = $app['data']->get($name);
            }
        }

    }

}