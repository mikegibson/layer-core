<?php

namespace Layer\Form;

use Silex\Application;

class FormServiceProvider extends \Silex\Provider\FormServiceProvider {

	public function register(Application $app) {

		parent::register($app);

		$app['form.extensions'] = $app->share($app->extend('form.extensions', function(array $extensions) use($app) {
			$extensions[] = new HtmlExtension();
			return $extensions;
		}));

		$app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function(array $extensions) use($app) {
			$extensions[] = new FormTypeLayerExtension();
			$extensions[] = new DateTypeExtension();
			$extensions[] = new TimeTypeExtension();
			return $extensions;
		}));

	}

}