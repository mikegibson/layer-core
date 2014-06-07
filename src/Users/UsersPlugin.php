<?php

namespace Layer\Users;

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

	}

	public function boot() {

		$this->app['users.repository'];

	}

}