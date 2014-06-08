<?php

namespace Layer\Pages;

use Layer\Node\ControllerNodeInterface;
use Layer\Plugin\Plugin;
use Silex\Application;

/**
 * Class PagesPlugin
 *
 * @package Layer\Pages
 */
class PagesPlugin extends Plugin {

	public function getName() {
		return 'pages';
	}

	public function register() {

		$app = $this->app;

		$app['pages.entity_class'] = 'Layer\\Pages\\Page';

		$app['pages.repository'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['pages.entity_class']);
		});

		$app['pages.root_node'] = $app->share(function() use($app) {
			return new PageNode($app['pages.repository']);
		});

		$app->extend('app.home_node', function(ControllerNodeInterface $node) use($app) {
			$node->adoptChildNodes($app['pages.root_node']);
			return $node;
		});

	}

	public function boot() {

		$this->app['pages.repository'];

	}

}