<?php

namespace Layer\Form;

use Silex\Application;

class FormServiceProvider extends \Silex\Provider\FormServiceProvider {

	public function register(Application $app) {

		parent::register($app);

		$app['form.extensions.html'] = $app->share(function() use($app) {
			return new HtmlExtension();
		});

		$app['form.extensions'] = $app->share($app->extend('form.extensions', function(array $extensions) use($app) {
			$extensions[] = $app['form.extensions.html'];
			return $extensions;
		}));

		$app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function(array $extensions) use($app) {
			$extensions[] = new FormTypeLayerExtension();
			return $extensions;
		}));

	}

}