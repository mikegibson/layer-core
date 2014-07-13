<?php

namespace Sentient\Data;

use Silex\Application;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;

class ValidatorServiceProvider extends \Silex\Provider\ValidatorServiceProvider {

	public function register(Application $app) {

		parent::register($app);

		$app['validator.mapping.class_metadata_factory'] = $app->share(function() use($app) {
			return new ClassMetadataFactory($app['annotations.loader']);
		});

	}

}