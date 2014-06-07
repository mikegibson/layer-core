<?php

namespace Layer\Pages;

use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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

			$pages = $this->app['controllers_factory'];

			$pages->get('/{node}', function(Request $request) {
					return $this->app['action_dispatcher']->dispatch($request->get('node'), $request);
				})
				->assert('node', '[a-z0-9\-/]*')
				->beforeMatch(function($attrs) {
					try {
						$node = trim($attrs['node'], '/');
						$attrs['node'] = $this->app['pages.root_node']->getDescendent($node);
					} catch(\InvalidArgumentException $e) {
						return false;
					}
					return $attrs;
				})
				->bind('pages');

			return $pages;

		});

	}

	public function boot() {

		$this->app['pages.repository'];

	}

}