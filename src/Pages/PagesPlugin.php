<?php

namespace Sentient\Pages;

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

		$app['pages.controllers'] = $app->share(function() use($app) {
			return $app['nodes.controllers_factory']($app['pages.root_node'], 'pages');
		});

	}

	public function boot(Application $app) {

		$app['pages.repository'];

	}

}