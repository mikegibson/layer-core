<?php

namespace Sentient\Users;

use Doctrine\ORM\EntityManagerInterface;
use Sentient\Node\ListNodeInterface;
use Sentient\Plugin\Plugin;
use Sentient\Users\Action\LoginAction;
use Sentient\Users\Command\AddUserCommand;
use Silex\Application;

class UsersPlugin extends Plugin {

	public function getName() {
		return 'users';
	}

	public function register(Application $app) {

		$app['users.entity_class'] = 'Sentient\\Users\\User';

		$app['users.repository'] = $app->share(function() use($app) {
			return $app['orm.rm']->loadRepository($app['orm.em'], $app['users.entity_class']);
		});

		$app['users.security_provider'] = $app->share(function() use($app) {
			return new UserSecurityProvider($app['users.repository']);
		});

		$app['users.listener'] = $app->share(function() use($app) {
			return new UserListener($app['security.encoder_factory']);
		});

		$app['orm.em'] = $app->share($app->extend('orm.em', function(EntityManagerInterface $entityManager) use($app) {
			$entityManager->getEventManager()->addEventSubscriber($app['users.listener']);
			return $entityManager;
		}));

		$app['console.commands.add_user'] = $app->share(function() {
			return new AddUserCommand();
		});

		$app['console'] = $app->share($app->extend('console', function(\Knp\Console\Application $consoleApp) use($app) {
			$consoleApp->add($app['console.commands.add_user']);
			return $consoleApp;
		}));

		$app['users.login_action'] = $app->share(function() use($app) {
			$action = new LoginAction(
				$app['form.factory'],
				$app['security.firewalls']['default']['form']['check_path']
			);
			$app->before(function() use($app, $action) {
				$action->setSecurityContext($app['security']);
			});
			return $action;
		});

		$app['security.firewalls'] = $app->share(function() use($app) {
			return [
				'default' => [
					'pattern' => '^/.*$',
					'anonymous' => true,
					'form' => [
						'login_path' => '/login',
						'check_path' => '/login-check',
						'username_parameter' => 'login[username]',
						'password_parameter' => 'login[password]',
						'default_target_path' => '/'
					],
					'remember_me' => [
						'key' => $app->share(function() use($app) {
								return sha1('remember_me' . $app['config']->read('salt'));
							}),
						'remember_me_property' => 'login[remember_me]',
						'path' => '/'
					],
					'logout' => [
						'logout_path' => '/logout'
					],
					'users' => $app->share(function() use($app) {
						return $app['users.security_provider'];
					})
				]
			];
		});

		$app['security.access_rules'] = $app->share(function() use($app) {
			return ['^/login-check$', 'ROLE_USER'];
		});

		$app['security.role_hierarchy'] = ['ROLE_ADMIN' => ['ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH']];

	}

	public function boot(Application $app) {

		$app['users.repository'];

		$app->match('/login', $app['actions.dispatch']($app['users.login_action']))->bind('login');

		$app->match('/logout')->bind('logout');

		$app['cms.navigation_list'] = $app->share($app->extend('cms.navigation_list', function(ListNodeInterface $cmsNav) use($app) {
			$cmsNav->getChild('users')->addChild('logout', 'Logout', $app['url_generator']->generate('logout'));
			return $cmsNav;
		}));

	}

}