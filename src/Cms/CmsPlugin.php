<?php

namespace Layer\Cms;

use Layer\Cms\Action\DashboardAction;
use Layer\Cms\Data\CmsRepository;
use Layer\Cms\Data\Metadata\Query\GetCmsFormFieldPropertyQuery;
use Layer\Cms\Data\Metadata\Query\GetCmsFormFieldsQuery;
use Layer\Cms\Node\CmsNavigationNode;
use Layer\Cms\Node\RepositoryCmsNodeFactory;
use Layer\Cms\Data\Metadata\Query\GetCmsEntityQuery;
use Layer\Cms\Data\Metadata\Query\GetCmsEntitySlugQuery;
use Layer\Cms\View\CmsHelper;
use Layer\Cms\View\TwigCmsExtension;
use Layer\Data\ManagedRepositoryEvent;
use Layer\Data\Metadata\QueryCollection;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeListNode;
use Layer\Node\WrappedControllerNode;
use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CmsProvider
 *
 * @package Layer\Cms\Provider
 */
class CmsPlugin extends Plugin {

	public function getName() {
		return 'cms';
	}

	public function register() {

		$this->app['cms.controllers'] = $this->app->share(function () {

			$cms = $this->app['controllers_factory'];

			$cms->match('/{node}', function(Request $request) {
					return $this->app['action_dispatcher']->dispatch($request->get('node'), $request);
				})
				->assert('node', '[a-z0-9\-/]*')
				->beforeMatch(function($attrs) {
					try {
						$nodePath = trim($attrs['node'], '/');
						$node = $this->app['cms.root_node'];
						if($nodePath !== '') {
							$node = $node->getDescendent($nodePath);;
						}
						$attrs['node'] = $node;
					} catch(\InvalidArgumentException $e) {
						return false;
					}
					return $attrs;
				})
				->bind('cms');

			return $cms;

		});

		$this->app['cms.helper'] = $this->app->share(function () {
			return new CmsHelper($this->app['url_generator']);
		});

		$this->app['twig'] = $this->app->share(
			$this->app->extend('twig', function (\Twig_Environment $twig) {

				$twig->addExtension(new TwigCmsExtension($this->app['cms.helper']));

				return $twig;

			})
		);

		$this->app['assets.js_cms'] = $this->app->share(function () {
			$asset = $this->app['assetic.factory']->createAsset([
				'@layer/js/jquery.js',
				'@cms/js/cms.js'
			], [
				'?uglifyjs'
			], [
				'output' => 'js/cms/cms.js'
			]);
			return $asset;
		});

		$this->app['assets.css_cms'] = $this->app->share(function () {
			$asset = $this->app['assetic.factory']->createAsset([
				'@cms/scss/cms.scss'
			], [
				'compass',
				'?uglifycss'
			], [
				'output' => 'css/cms/cms.css'
			]);
			return $asset;
		});

		$this->app['metadata.queries.getCmsEntity'] = $this->app->share(function() {
			return new GetCmsEntityQuery($this->app['annotations.reader']);
		});

		$this->app['metadata.queries.getCmsEntitySlug'] = $this->app->share(function() {
			return new GetCmsEntitySlugQuery($this->app['metadata.queries.getCmsEntity'], $this->app['inflector']);
		});

		$this->app['metadata.queries.getCmsFormFieldProperty'] = $this->app->share(function() {
			return new GetCmsFormFieldPropertyQuery(
				$this->app['annotations.reader'],
				$this->app['metadata.queries.getPropertyOrm']
			);
		});

		$this->app['metadata.queries.getCmsFormFields'] = $this->app->share(function() {
			return new GetCmsFormFieldsQuery(
				$this->app['metadata.queries.getEditableProperties'],
				$this->app['metadata.queries.getCmsFormFieldProperty']
			);
		});

		$this->app->extend(
			'metadata.queries_collection',
			function(QueryCollection $collection) {
				$collection
					->registerQuery($this->app['metadata.queries.getCmsEntity'])
					->registerQuery($this->app['metadata.queries.getCmsEntitySlug'])
					->registerQuery($this->app['metadata.queries.getCmsFormFieldProperty'])
					->registerQuery($this->app['metadata.queries.getCmsFormFields']);
				return $collection;
			}
		);

		$this->app['cms.actions.dashboard'] = $this->app->share(function() {
			return new DashboardAction();
		});

		$this->app['cms.repository_node_factory'] = $this->app->share(function() {
			return new RepositoryCmsNodeFactory($this->app);
		});

		$this->app['cms.dashboard_node'] = $this->app->share(function() {
			return new ControllerNode('cms', $this->app['cms.actions.dashboard']);
		});

		$this->app['cms.root_node'] = $this->app->share(function() {
			return new WrappedControllerNode($this->app['cms.dashboard_node']);
		});

		$this->app['cms.navigation_list'] = $this->app->share(function() {
			$node = new CmsNavigationNode($this->app['cms.root_node'], $this->app['url_generator']);
			$dashboardNode = new ControllerNodeListNode($this->app['cms.dashboard_node'], $this->app['url_generator'], $node);
			$node->registerChildNode($dashboardNode, false, true);
			return $node;
		});

		$this->app->extend('dispatcher', function(EventDispatcherInterface $dispatcher) {
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