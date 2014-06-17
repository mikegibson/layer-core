<?php

namespace Layer\Plugin;

use Silex\Application;
use Silex\ServiceProviderInterface;

abstract class Plugin implements ServiceProviderInterface {

	protected $depends = [];

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