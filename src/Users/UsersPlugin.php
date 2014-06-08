<?php

namespace Layer\Users;

use Doctrine\ORM\EntityManagerInterface;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;
use Layer\Plugin\Plugin;
use Layer\Users\Action\LoginAction;
use Layer\Users\Form\LoginFormType;

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

		$this->app['users.login_form_type'] = $this->app->share(function() {
			return new LoginFormType();
		});

		$this->app['users.login_action'] = $this->app->share(function() {
			return new LoginAction($this->app['form.factory'], $this->app['users.login_form_type']);
		});

		$this->app['users.login_node'] = $this->app->share(function() {
			return new ControllerNode('login', $this->app['users.login_action']);
		});

		$this->app->extend('orm.em', function(EntityManagerInterface $entityManager) {
			$entityManager->getEventManager()->addEventSubscriber($this->app['users.listener']);
			return $entityManager;
		});

		$this->app->extend('app.home_node', function(ControllerNodeInterface $node) {
			$node->wrapChildNode($this->app['users.login_node']);
			return $node;
		});

	/*	$firewalls = $this->app['security.firewalls'];
		$firewalls['form'] = [
			'form' => [
				'login_path' => '/login',
				'check_path' => '/login'
			]
		];
		$this->app['security.firewalls'] = $firewalls;
*/
	}

	public function boot() {

		$this->app['users.repository'];

	}

}