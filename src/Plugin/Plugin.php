<?php

namespace Layer\Plugin;

use Layer\Application;

abstract class Plugin {

	protected $app;

	protected $depends = [];

	private $__path;

	public function __construct(Application $app) {

		$this->app = $app;
	}

	public function register() {

	}

	public function boot() {

	}

	abstract public function getName();

	public function getPath() {

		if ($this->__path === null) {
			$reflection = new \ReflectionClass($this);
			$this->__path = dirname($reflection->getFileName());
		}

		return $this->__path;
	}

	public function getDependencies() {

		return $this->depends;
	}

	public function __get($name) {

		if ($name === 'name') {
			return $this->getName();
		}
	}

}