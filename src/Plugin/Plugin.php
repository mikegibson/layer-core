<?php

namespace Layer\Plugin;

use Silex\Application;
use Silex\ServiceProviderInterface;

abstract class Plugin implements ServiceProviderInterface {

	protected $depends = [];

	private $__path;

	public function register(Application $app) {

	}

	public function boot(Application $app) {

	}

	abstract public function getName();

	public function getPath() {

		if ($this->__path === null) {
			$reflection = new \ReflectionClass($this);
			$this->__path = dirname($reflection->getFileName());
		}

		return $this->__path;
	}

}