<?php

namespace Sentient\Pages;

use Sentient\Node\ControllerNodeInterface;
use Sentient\Plugin\Plugin;
use Silex\Application;

/**
 * Class PagesPlugin
 *
 * @package Sentient\Pages
 */
class PagesPlugin extends Plugin {

	public function getName() {
		return 'pages';
	}

	public function register(Application $app) {

		$app['pages.entity_class'] = 'Sentient\\Pages\\Page';

		$app['pages.repository'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['pages.entity_class']);
		});

		$app['pages.root_node'] = $app->share(function() use($app) {
			return new PageNode($app['pages.repository']);
		});

		$app['home_node'] = $app->share($app->extend('home_node', function(ControllerNodeInterface $node) use($app) {
			$node->adoptChildren($app['pages.root_node']);
			return $node;
		}));

	}

	public function boot(Application $app) {

		$app['pages.repository'];

	}

}