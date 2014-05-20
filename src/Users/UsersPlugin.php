<?php

namespace Layer\Users;

use Layer\Plugin\Plugin;

class UsersPlugin extends Plugin {

	protected $name = 'users';

	public function register() {

	}

	public function boot() {
		$this->app['orm.rm']->loadRepository($this->app['orm.em'], 'Layer\\Users\\User');
	}

}