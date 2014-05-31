<?php

namespace Layer\Cms;

use Layer\Cms\Action\DashboardAction;
use Layer\Cms\Data\CmsRepository;
use Layer\Cms\Data\Metadata\Query\GetCmsFormFieldPropertyQuery;
use Layer\Cms\Data\Metadata\Query\GetCmsFormFieldsQuery;
use Layer\Cms\Node\RepositoryCmsNodeFactory;
use Layer\Cms\Node\RootCmsNode;
use Layer\Cms\Data\Metadata\Query\GetCmsEntityQuery;
use Layer\Cms\Data\Metadata\Query\GetCmsEntitySlugQuery;
use Layer\Cms\View\CmsHelper;
use Layer\Cms\View\TwigCmsExtension;
use Layer\Controller\NodeController;
use Layer\Data\ManagedRepositoryEvent;
use Layer\Data\Metadata\QueryCollection;
use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CmsProvider
 *
 * @package Layer\Cms\Provider
 */
class CmsPlugin extends Plugin {

	protected $name = 'cms';

	public function register() {

		$app = $this->app;

		$app['cms.node_controller'] = $app->share(function() use($app) {
			return new NodeController($app['cms.root_node'], $app['twig.view']);
		});

		$app['cms.controllers'] = $app->share(function () use ($app) {

			$cms = $app['controllers_factory'];

			$cms->match('/{node}', 'cms.node_controller:dispatch')
				->assert('node', '[a-z0-9\-/]+')
				->bind('cms');

			return $cms;

		});

		$app['cms.helper'] = $app->share(function () use ($app) {
			return new CmsHelper($app['url_generator']);
		});

		$app['twig'] = $app->share(
			$app->extend('twig', function (\Twig_Environment $twig) use ($app) {

				$twig->addExtension(new TwigCmsExtension($app['cms.helper']));

				return $twig;

			})
		);

		$app['assets.js_cms'] = $app->share(function () use ($app) {
			$asset = $app['assetic.factory']->createAsset([
				'@layer/js/jquery.js',
				'@cms/js/cms.js'
			], [
				'?uglifyjs'
			], [
				'output' => 'js/cms/cms.js'
			]);
			return $asset;
		});

		$app['assets.css_cms'] = $app->share(function () use ($app) {
			$asset = $app['assetic.factory']->createAsset([
				'@cms/scss/cms.scss'
			], [
				'compass',
				'?uglifycss'
			], [
				'output' => 'css/cms/cms.css'
			]);
			return $asset;
		});

		$app['metadata.queries.getCmsEntity'] = $app->share(function() use($app) {
			return new GetCmsEntityQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getCmsEntitySlug'] = $app->share(function() use($app) {
			return new GetCmsEntitySlugQuery($app['metadata.queries.getCmsEntity'], $app['inflector']);
		});

		$app['metadata.queries.getCmsFormFieldProperty'] = $app->share(function() use($app) {
			return new GetCmsFormFieldPropertyQuery(
				$app['annotations.reader'],
				$app['metadata.queries.getPropertyOrm']
			);
		});

		$app['metadata.queries.getCmsFormFields'] = $app->share(function() use($app) {
			return new GetCmsFormFieldsQuery(
				$app['metadata.queries.getEditableProperties'],
				$app['metadata.queries.getCmsFormFieldProperty']
			);
		});

		$app['metadata.queries_collection'] = $app->extend(
			'metadata.queries_collection',
			function(QueryCollection $collection) use($app) {
				$collection
					->registerQuery($app['metadata.queries.getCmsEntity'])
					->registerQuery($app['metadata.queries.getCmsEntitySlug'])
					->registerQuery($app['metadata.queries.getCmsFormFieldProperty'])
					->registerQuery($app['metadata.queries.getCmsFormFields']);
				return $collection;
			}
		);

		$app['cms.actions.dashboard'] = $app->share(function() {
			return new DashboardAction();
		});

		$app['cms.repository_node_factory'] = $app->share(function() use($app) {
			return new RepositoryCmsNodeFactory($app);
		});

		$app['cms.root_node'] = $app->share(function() use($app) {
			return new RootCmsNode($app['cms.actions.dashboard']);
		});

		$app->extend('dispatcher', function(EventDispatcherInterface $dispatcher) use($app) {
			$dispatcher->addListener(ManagedRepositoryEvent::REGISTER, [$this, 'onRegisterRepository']);
			return $dispatcher;
		});

	}

	public function boot() {

		$this->app['assetic.asset_manager']->set('js_cms', $this->app['assets.js_cms']);
		$this->app['assetic.asset_manager']->set('css_cms', $this->app['assets.css_cms']);

	}

	public function onRegisterRepository(ManagedRepositoryEvent $event) {
		$baseRepository = $event->getRepository();
		$repository = new CmsRepository($baseRepository, $this->app['cms.repository_node_factory']);
		$event->setRepository($repository);
	}

}