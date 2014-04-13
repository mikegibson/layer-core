<?php

namespace Layer\Data;

/**
 * Class Blueprint
 *
 * @package Layer\Data
 */
class Blueprint extends \Illuminate\Database\Schema\Blueprint {

	/**
	 * Overriding class to make this a public method
	 *
	 * @param $type
	 * @param $name
	 * @param array $parameters
	 * @return \Illuminate\Support\Fluent
	 */
	public function add($type, $name, array $parameters = array()) {
		return $this->addColumn($type, $name, $parameters);
	}

}