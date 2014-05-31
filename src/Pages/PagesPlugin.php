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

	protected $name = 'pages';

	public function register() {

		$app = $this->app;

		$app['pages.controllers'] = $app->share(function () use ($app) {

			$pages = $app['controllers_factory'];

			$pages->get('/{record}', 'pages.controllers.pages_controller:dispatch')
				->value('action', 'view')
				->convert('record', function ($page) use ($app) {

					$query = $app['data.content/pages']->query()->where('slug', $page);

					if (!$record = $query->first()) {
						$app->abort(404);
					}

					return $record;

				});

			return $pages;

		});

		$app['pages.controllers.pages_controller'] = $app->share(function () use ($app) {

			return new PagesController($app);

		});

	}

	public function boot() {
		$this->app['orm.rm']->loadRepository($this->app['orm.em'], 'Layer\\Pages\\Page');
	}

}