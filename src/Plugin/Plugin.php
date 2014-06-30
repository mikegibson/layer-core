<?php

namespace Sentient\Plugin;

use Silex\Application;

abstract class Plugin implements PluginInterface {

	private $path;

	public function register(Application $app) {

	}

	public function boot(Application $app) {

	}

	public function getPath() {

		if ($this->path === null) {
			$reflection = new \ReflectionClass($this);
			$this->path = dirname($reflection->getFileName());
		}

		return $this->path;
	}

}