<?php

namespace Layer\Utility;

trait SetPropertiesTrait {

	private $propertyKeys;

	/**
	 * Allows setting of multiple properties of the object in a single line of code. Will only set
	 * properties that are part of a class declaration.
	 *
	 * @param array $properties An associative array containing properties and corresponding values.
	 * @return void
	 */
	protected function _setProperties(array $properties = []) {

		if ($properties !== []) {
			$properties = array_intersect_key($properties, array_flip($this->__propertyKeys()));
			foreach ($properties as $key => $val) {
				$this->{$key} = $val;
			}
		}
	}

	private function __propertyKeys() {

		if ($this->propertyKeys === null) {
			$this->propertyKeys = array_keys(get_object_vars($this));
		}

		return $this->propertyKeys;
	}

}