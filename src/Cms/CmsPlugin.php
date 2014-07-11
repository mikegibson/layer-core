<?php

namespace Sentient\Cms;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Sentient\Action\ReskinnedAction;
use Sentient\Cms\Action\AddAction;
use Sentient\Cms\Action\EditAction;
use Sentient\Cms\Action\IndexAction;
use Sentient\Cms\Data\HtmlStripperDecorator;
use Sentient\Cms\Data\LinkerDecorator;
use Sentient\Cms\Data\Metadata\Query\GetCmsNodePathQuery;
use Sentient\Cms\Data\Metadata\Query\GetCmsNodeQuery;
use Sentient\Cms\Data\Metadata\Query\GetRootCmsNodeQuery;
use Sentient\Cms\Data\Metadata\Query\HasCmsNodeQuery;
use Sentient\Cms\Node\CmsNavigationNode;
use Sentient\Cms\Node\CmsNodeRegistry;
use Sentient\Cms\Node\RepositoryCmsNodeFactory;
use Sentient\Cms\Data\Metadata\Query\GetCmsEntityQuery;
use Sentient\Cms\Data\Metadata\Query\GetCmsEntitySlugQuery;
use Sentient\Cms\View\CmsHelper;
use Sentient\Cms\View\TwigCmsExtension;
use Sentient\Data\ManagedRepositoryEvent;
use Sentient\Data\Metadata\QueryCollection;
use Sentient\Data\TableData\ChainedTableDataDecorator;
use Sentient\Data\TableData\EscaperDecorator;
use Sentient\Data\TableData\StringifierDecorator;
use Sentient\Data\TableData\TruncatorDecorator;
use Sentient\Media\Image\FilterRegistry;
use Sentient\Media\Image\ImageTransformer;
use Sentient\Node\ControllerNode;
use Sentient\Plugin\Plugin;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class CmsProvider
 *
 * @package Sentient\Cms\Provider
 */
class CmsPlugin extends Plugin {

	public function getName() {
		return 'cms';
	}

	public function register(Application $app) {

		if(!isset($app['cms.url_fragment'])) {
			$app['cms.url_fragment'] = 'cms';
		}

		$app['cms.helper'] = $app->share(function() use($app) {
			return new CmsHelper($app['url_generator']);
		});

		$app['cms.node_registry'] = $app->share(function() use($app) {
			return new CmsNodeRegistry();
		});

		$app['metadata.queries.getCmsEntity'] = $app->share(function() use($app) {
			return new GetCmsEntityQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getCmsEntitySlug'] = $app->share(function() use($app) {
			return new GetCmsEntitySlugQuery($app['annotations.reader'], $app['metadata.queries.getEntityName']);
		});

		$app['metadata.queries.getCmsNodePath'] = $app->share(function() use($app) {
			return new GetCmsNodePathQuery($app['annotations.reader']);
		});

		$app['metadata.queries.getCmsNode'] = $app->share(function() use($app) {
			return new GetCmsNodeQuery($app['orm.manager_registry'], $app['cms.node_registry']);
		});

		$app['metadata.queries.hasCmsNode'] = $app->share(function() use($app) {
			return new HasCmsNodeQuery($app['orm.manager_registry'], $app['cms.node_registry']);
		});

		$app['metadata.queries.getRootCmsNode'] = $app->share(function() use($app) {
			return new GetRootCmsNodeQuery($app['orm.manager_registry'], $app['cms.node_registry']);
		});

		$app['metadata.queries'] = $app->share($app->extend('metadata.queries',
			function(QueryCollection $collection) use($app) {
				$collection
					->registerQuery($app['metadata.queries.getCmsEntity'])
					->registerQuery($app['metadata.queries.getCmsEntitySlug'])
					->registerQuery($app['metadata.queries.getCmsNodePath'])
					->registerQuery($app['metadata.queries.getCmsNode'])
					->registerQuery($app['metadata.queries.hasCmsNode'])
					->registerQuery($app['metadata.queries.getRootCmsNode'])
				;
				return $collection;
			}
		));

		$app['paginator.decorators.cmsTable'] = $app->share(function() use($app) {
			return new ChainedTableDataDecorator([
				new StringifierDecorator(),
				new HtmlStripperDecorator($app['orm.rm']),
				new TruncatorDecorator($app['string_helper']),
				new EscaperDecorator()
			]);
		});

		$app['paginator.decorators.cmsIndex'] = $app->share(function() use($app) {
			return new ChainedTableDataDecorator([
				$app['paginator.decorators.cmsTable'],
				new LinkerDecorator($app['orm.rm'], $app['url_generator'])
			]);
		});

		$app['cms.actions'] = $app->share(function() use($app) {
			return [
				new IndexAction($app['property_accessor'], $app['paginator.decorators.cmsIndex']),
				new AddAction($app['form.factory'], $app['url_generator']),
				new EditAction($app['form.factory'], $app['url_generator'])
			];
		});

		$app['cms.repository_node_factory'] = $app->share(function() use($app) {
			return new RepositoryCmsNodeFactory($app['cms.root_node'], $app['cms.actions']);
		});

		$app['cms.login_action'] = $app->share(function() use($app) {
			return new ReskinnedAction($app['users.login_action'], '@cms/view/login');
		});

		$app['cms.login_node'] = $app->share(function() use($app) {
			return new ControllerNode($app['cms.login_action']);
		});

		$app['cms.content_node'] = $app->share(function() use($app) {
			return new ControllerNode(null, null, 'content', 'Content', null, true, false);
		});

		$app['cms.root_node'] = $app->share(function() use($app) {
			$node = new ControllerNode();
			$node->wrapChild($app['cms.login_node']);
			$node->wrapChild($app['cms.content_node']);
			return $node;
		});

		$app['cms.controllers'] = $app->share(function() use($app) {
			return $app['nodes.controllers_factory']($app['cms.root_node'], 'cms', 'node', false);
		});

		$app['cms.navigation_list'] = $app->share(function() use($app) {
			$node = new CmsNavigationNode($app['cms.root_node'], 'cms', $app['url_generator']);
			return $node;
		});

		$app['dispatcher'] = $app->share($app->extend('dispatcher', function(EventDispatcherInterface $dispatcher) use($app) {
			$dispatcher->addListener(ManagedRepositoryEvent::REGISTER, function(ManagedRepositoryEvent $event) use($app) {
				$repository = $event->getRepository();
				$nodes = $app['cms.repository_node_factory']->createNodes($repository);
				$isRootNode = true;
				foreach($nodes as $node) {
					$app['cms.node_registry']->register($repository, $node, $isRootNode);
					$isRootNode = false;
				}
			});
			return $dispatcher;
		}));

		if(!isset($app['cms.access_rule'])) {
			$app['cms.access_rule'] = ['^/' . $app['cms.url_fragment'] . '/.*$', 'ROLE_ADMIN'];
		}

		$app['images.filters.cms_thumbnail'] = $app->share(function() use($app) {
			$filter = new ImageTransformer('cms-thumbnail');
			$filter->getTransformation()->thumbnail(new Box(60, 60), ImageInterface::THUMBNAIL_OUTBOUND);
			return $filter;
		});

	}

	public function boot(Application $app) {
		$fragment = $app['cms.url_fragment'];
		$app->mount("/{$fragment}", $app['cms.controllers']);
		$rules = $app['security.access_rules'];
		$rules[] = $app['cms.access_rule'];
		$app['security.access_rules'] = $rules;

		$app['twig'] = $app->share($app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addExtension(new TwigCmsExtension($app['cms.helper']));
			return $twig;
		}));

		$app['images.filters'] = $app->share($app->extend('images.filters', function(FilterRegistry $filters) use($app) {
			$filters->addFilter($app['images.filters.cms_thumbnail']);
			return $filters;
		}));

		$app['assets.register_scss']('cms/main', '@cms/scss/main.scss');
		$app['assets.register_scss']('cms/header', '@cms/scss/header.scss');

		$app['assetic.asset_manager']->set('js_cms_header', $app['assetic.factory']->createAsset(
			[
				'@sentient/js/dropdown.js',
				'@cms/js/cms-header.js'
			],
			[],
			['output' => 'js/cms/header.js']
		));

		$scripts = [];

		$json = file_get_contents($this->getPath() . '/Resource/js/bower.json');
		$bower = json_decode($json);
		foreach($bower->dependencies as $name => $version) {
			$scripts[] = '@cms/js/bower_components/' . $name . '/' . $name . '.js';
		}

		$scripts[] = '@cms/js/cms-panel.js';
		$scripts[] = '@cms/js/cms-form.js';
		$scripts[] = '@cms/js/cms-html-widget.js';
		$scripts[] = '@cms/js/cms.js';

		$cmsJs = $app['assetic.factory']->createAsset(
			(array) $scripts,
			['uglifyjs'],
			['output' => 'js/cms/main.js']
		);

		$app['assetic.asset_manager']->set('js_cms', $cmsJs);

	}

}