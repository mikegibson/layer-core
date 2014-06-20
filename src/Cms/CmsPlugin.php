<?php

namespace Layer\Cms;

use Layer\Action\ReskinnedAction;
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
use Layer\Node\WrappedControllerNode;
use Layer\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CmsProvider
 *
 * @package Layer\Cms\Provider
 */
class CmsPlugin extends Plugin {

	public function getName() {
		return 'cms';
	}

	public function register(Application $app) {

		if(!isset($app['cms.url_fragment'])) {
			$app['cms.url_fragment'] = 'cms';
		}

		$app['cms.controllers'] = $app->share(function() use($app) {
			return $app['nodes.controllers_factory']($app['cms.root_node'], 'node', false);
		});

		$app['cms.helper'] = $app->share(function() use($app) {
			return new CmsHelper($app['url_generator']);
		});

		$app[$app->assets['js_cms'] = 'assets.js_cms'] = $app->share(function() use($app) {
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

		$app[$app->assets['css_cms'] = 'assets.css_cms'] = $app->share(function() use($app) {
			return $app['assetic.factory']->createAsset([
				'@cms/scss/cms.scss',
				'@cms/scss/cms_header.scss'
			], [
				'compass',
				'?uglifycss'
			], [
				'output' => 'css/cms/cms.css'
			]);
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
				$app['metadata.queries.getPropertyLabel'],
				$app['metadata.queries.isHtmlProperty']
			);
		});

		$app['metadata.queries.getCmsFormFields'] = $app->share(function() use($app) {
			return new GetCmsFormFieldsQuery(
				$app['metadata.queries.getEditableProperties'],
				$app['metadata.queries.getCmsFormFieldProperty']
			);
		});

		$app['metadata.queries'] = $app->share($app->extend('metadata.queries',
			function(QueryCollection $collection) use($app) {
				$collection
					->registerQuery($app['metadata.queries.getCmsEntity'])
					->registerQuery($app['metadata.queries.getCmsEntitySlug'])
					->registerQuery($app['metadata.queries.getCmsFormFieldProperty'])
					->registerQuery($app['metadata.queries.getCmsFormFields']);
				return $collection;
			}
		));

		$app['cms.repository_node_factory'] = $app->share(function() use($app) {
			return new RepositoryCmsNodeFactory($app);
		});

		$app['cms.dashboard_node'] = $app->share(function() use($app) {
			return new ControllerNode('cms', $app['cms.actions.dashboard']);
		});

		$app['cms.login_action'] = $app->share(function() use($app) {
			return new ReskinnedAction($app['users.login_action'], '@cms/view/login');
		});

		$app['cms.login_node'] = $app->share(function() use($app) {
			return new ControllerNode('cms_login', $app['cms.login_action']);
		});

		$app['cms.root_node'] = $app->share(function() use($app) {
			$node = new ControllerNode('cms');
			$node->wrapChildNode($app['cms.login_node']);
			return $node;
		});

		$app['cms.navigation_list'] = $app->share(function() use($app) {
			$node = new CmsNavigationNode($app['cms.root_node'], $app['url_generator']);
			return $node;
		});

		$app['dispatcher'] = $app->share($app->extend('dispatcher', function(EventDispatcherInterface $dispatcher) use($app) {
			$dispatcher->addListener(ManagedRepositoryEvent::REGISTER, function(ManagedRepositoryEvent $event) use($app) {
				$baseRepository = $event->getRepository();
				$repository = new CmsRepository($baseRepository, $app['cms.repository_node_factory']);
				$event->setRepository($repository);
			});
			return $dispatcher;
		}));

		if(!isset($app['cms.access_rule'])) {
			$app['cms.access_rule'] = ['^/' . $app['cms.url_fragment'] . '/.*$', 'ROLE_ADMIN'];
		}

		$app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addExtension(new TwigCmsExtension($app['cms.helper']));
			return $twig;
		}));

	}

	public function boot(Application $app) {
		$fragment = $app['cms.url_fragment'];
		$app->mount("/{$fragment}", $app['cms.controllers']);
		$rules = $app['security.access_rules'];
		$rules[] = $app['cms.access_rule'];
		$app['security.access_rules'] = $rules;
	}

}