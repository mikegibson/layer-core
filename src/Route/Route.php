<?php

namespace Sentient\Route;

use Silex\Route as BaseRoute;

class Route extends BaseRoute {

	private $matchCallbacks = [];

	public function beforeMatch($callable) {
		if(!is_callable($callable)) {
			throw new \InvalidArgumentException('The passed parameter was not callable.');
		}
		$this->matchCallbacks[] = $callable;
	}

	public function getMatchCallbacks() {
		return $this->matchCallbacks;
	}

}