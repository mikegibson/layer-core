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
use Layer\Users\Action\LoginAction;
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

	public function register() {
		
		$app = $this->app;

		$app['cms.url_fragment'] = 'cms';

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
				$app['metadata.queries.getPropertyLabel']
			);
		});

		$app['metadata.queries.getCmsFormFields'] = $app->share(function() use($app) {
			return new GetCmsFormFieldsQuery(
				$app['metadata.queries.getEditableProperties'],
				$app['metadata.queries.getCmsFormFieldProperty']
			);
		});

		$app->extend(
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

		$app['cms.dashboard_node'] = $app->share(function() use($app) {
			return new ControllerNode('cms', $app['cms.actions.dashboard']);
		});

		$app['cms.login_action'] = $app->share(function() use($app) {
			return new LoginAction(
				$app['form.factory'],
				$app['security.firewalls']['cms']['form']['check_path'],
				'@cms/view/login'
			);
		});

		$app['cms.login_node'] = $app->share(function() use($app) {
			return new ControllerNode('cms_login', $app['cms.login_action']);
		});

		$app['cms.root_node'] = $app->share(function() use($app) {
			$node = new WrappedControllerNode($app['cms.dashboard_node']);
			$node->wrapChildNode($app['cms.login_node']);
			return $node;
		});

		$app['cms.navigation_list'] = $app->share(function() use($app) {
			$node = new CmsNavigationNode($app['cms.root_node'], $app['url_generator']);
			$dashboardNode = new ControllerNodeListNode($app['cms.dashboard_node'], $app['url_generator'], $node);
			$node->registerChildNode($dashboardNode, false, true);
			return $node;
		});

		$callback = [$this, 'onRegisterRepository'];
		$app->extend('dispatcher', function(EventDispatcherInterface $dispatcher) use($callback) {
			$dispatcher->addListener(ManagedRepositoryEvent::REGISTER, $callback);
			return $dispatcher;
		});

		$app->extend('twig', function(\Twig_Environment $twig) use($app) {
			$twig->addExtension(new TwigCmsExtension($app['cms.helper']));
			return $twig;
		});

		$app->extend('security.firewalls', function(array $firewalls) use($app) {

			$fragment = $app['cms.url_fragment'];

			$firewalls['cms_login'] = [
				'pattern' => "^/{$fragment}/login(/?)$"
			];

			$firewalls['cms'] = [
				'pattern' => "^/{$fragment}(/.*)?$",
				'form' => [
					'login_path' => "/{$fragment}/login",
					'check_path' => "/{$fragment}/login-check",
					'username_parameter' => 'login[username]',
					'password_parameter' => 'login[password]',
					'default_target_path' => "/{$fragment}"
				],
				'remember_me' => [
					'key' => $app->share(function() use($app) {
							$string = 'remember_me' . $app['config']->read('salt');
							return md5($string);
						}),
					'remember_me_property' => 'login[remember_me]',
					'path' => '/'
				],
				'logout' => [
					'logout_path' => "/{$fragment}/logout"
				],
				'users' => $app->share(function() use($app) {
						return $app['users.security_provider'];
					})
			];

			return $firewalls;

		});

	}

	public function boot() {
		$fragment = $this->app['cms.url_fragment'];
		$this->app->mount("/{$fragment}", $this->app['cms.controllers']);
	}

	public function onRegisterRepository(ManagedRepositoryEvent $event) {
		$baseRepository = $event->getRepository();
		$repository = new CmsRepository($baseRepository, $this->app['cms.repository_node_factory']);
		$event->setRepository($repository);
	}

}