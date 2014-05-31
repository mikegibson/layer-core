<?php

namespace Layer\Pages;

use Layer\Cms\Node\RepositoryCmsNode;
use Layer\Cms\RootCmsNode;
use Layer\Data\RepositoryManager;
use Layer\Node\WrappedNode;
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

	//	$app['cms.nodes.pages'] = $app->share(function() use($app) {
	//		return new RepositoryCmsNode($app['orm.rm']->getRepository('pages'));
	//	});

	}

	public function boot() {
		$this->app['orm.rm']->loadRepository($this->app['orm.em'], 'Layer\\Pages\\Page');
	//	if(isset($this->app['cms.root_node'])) {

	//		$this->app['cms.root_node']->wrapChildNode($this->app['cms.nodes.pages']);

		//	die('here');

			/*$app['admin.cms.root_node'] = $app->extend('admin.cms.root_node', function(RootCmsNode $rootNode) use($app) {
				$rootNode->addChildNode();
			});
			$rootNode = $app['admin.cms.root_node'];
			$p
			$rootNode->registerChildNode*/
	//	}
	}

}