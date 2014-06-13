<?php

namespace Layer\Users;

use Doctrine\ORM\EntityManagerInterface;
use Layer\Plugin\Plugin;
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

		$app->extend('orm.em', function(EntityManagerInterface $entityManager) use($app) {
			$entityManager->getEventManager()->addEventSubscriber($app['users.listener']);
			return $entityManager;
		});

	}

	public function boot(Application $app) {

		$app['users.repository'];

	}

}