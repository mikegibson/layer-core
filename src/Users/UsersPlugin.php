<?php

namespace Layer\Users;

use Doctrine\ORM\EntityManagerInterface;
use Layer\Plugin\Plugin;
use Layer\Users\Command\AddUserCommand;
use Silex\Application;

class UsersPlugin extends Plugin {

	public function getName() {
		return 'users';
	}

	public function register(Application $app) {

		$app['users.entity_class'] = 'Layer\\Users\\User';

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

	}

	public function boot(Application $app) {

		$app['users.repository'];

	}

}