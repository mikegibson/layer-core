<?php

namespace Layer\Asset;

use Layer\Application;

class AssetFactory extends \Assetic\Factory\AssetFactory {

	protected $app;

	public function __construct(Application $app) {
		parent::__construct($app['path_resources'], $app->config('debug'));
		$this->app = $app;
	}

	protected function parseInput($input, array $options = []) {
		if (preg_match('/^@([a-z_]+)(\/.+)$/', $input, $matches)) {
			list(, $namespace, $resource) = $matches;
			$paths = [];
			if ($namespace === 'layer') {
				$paths = [
					$this->app['path_templates'] . '/layer' . $resource,
					$this->app['path_layer_resources'] . $resource
				];
			} elseif ($plugin = $this->app['plugins']->get($namespace)) {
				$paths = [
					$this->app['path_resources'] . '/plugin/' . $plugin->name . $resource,
					$plugin->getPath() . '/Resource' . $resource
				];
			}
			foreach ($paths as $path) {
				if (file_exists($path)) {
					$input = $path;
					$options['root'] = [];
					break;
				}
			}
		}
		return parent::parseInput($input, $options);
	}

}