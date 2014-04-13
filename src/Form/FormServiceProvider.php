<?php

namespace Layer\Form;

use Silex\Application;

class FormServiceProvider extends \Silex\Provider\FormServiceProvider {

	public function register(Application $app) {

		// @todo set $app['form.secret'] to something better

		parent::register($app);

	}

}