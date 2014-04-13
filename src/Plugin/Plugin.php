<?php

namespace Layer\Plugin;

use Layer\Application;
use Symfony\Component\Debug\Exception\FatalErrorException;

abstract class Plugin {

	protected $app;

	protected $name;

	protected $depends = [];

	private $__path;

	public function __construct(Application $app) {

		$this->app = $app;
		if (empty($this->name)) {
			throw new FatalErrorException('No plugin name was specified!');
		}
	}

	public function register() {

	}

	public function boot() {

	}

	public function getName() {

		return $this->name;
	}

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