<?php

namespace Layer\Pages;

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

		$this->app['pages.entity_class'] = 'Layer\\Pages\\Page';

		$this->app['pages.repository'] = $this->app->share(function() {
			return $this->app['orm.rm']->loadRepository($this->app['orm.em'], $this->app['pages.entity_class']);
		});

		$this->app['pages.root_node'] = $this->app->share(function() {
			return new PageNode($this->app['pages.repository']);
		});

		$this->app['pages.controllers'] = $this->app->share(function () {
			return $this->app['nodes.controllers_factory']($this->app['pages.root_node']);
		});

	}

	public function boot() {

		$this->app['pages.repository'];

	}

}