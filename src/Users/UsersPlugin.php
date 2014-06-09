<?php

namespace Layer\Users;

use Doctrine\ORM\EntityManagerInterface;
use Layer\Plugin\Plugin;

class UsersPlugin extends Plugin {

	public function getName() {
		return 'users';
	}

	public function register() {

		$this->app['users.entity_class'] = 'Layer\\Users\\User';

		$this->app['users.repository'] = $this->app->share(function() {
			return $this->app['orm.rm']->loadRepository($this->app['orm.em'], $this->app['users.entity_class']);
		});

		$this->app['users.security_provider'] = $this->app->share(function() {
			return new UserSecurityProvider($this->app['users.repository']);
		});

		$this->app['users.listener'] = $this->app->share(function() {
			return new UserListener($this->app['security.encoder_factory']);
		});

		$this->app->extend('orm.em', function(EntityManagerInterface $entityManager) {
			$entityManager->getEventManager()->addEventSubscriber($this->app['users.listener']);
			return $entityManager;
		});

	}

	public function boot() {

		$this->app['users.repository'];

	}

}