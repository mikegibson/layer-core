<?php

namespace Sentient\Utility;

/**
 * Class CacheVarsTrait
 * Allows getter functions to calculate vars just once when first accessed via a magic method,
 * which are stored for later usage
 *
 * @package Sentient\Utility
 */
trait CacheVarsTrait {

	/**
	 * @var array
	 */
	private $cachedVars = [];

	/**
	 * @param $name
	 * @return mixed
	 */
	public function __get($name) {

		$method = '_get' . ucfirst($name);
		if (method_exists($this, $method)) {
			if (!isset($this->cachedVars[$name])) {
				$this->cachedVars[$name] = call_user_func(array($this, $method));
			}

			return $this->cachedVars[$name];
		}
	}

}