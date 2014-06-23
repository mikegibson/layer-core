<?php

namespace Layer\Data\TableData;

class StringifierDecorator implements TableDataDecoratorInterface {

	public function decorateColumns(array $columns) {
		return $columns;
	}

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return string
	 */
	public function decorateData($value, $key, $object) {
		return $this->stringify($value);
	}

	protected function stringify($value, $force = true) {
		if(is_array($value)) {
			return $this->stringifyArray($value);
		} elseif(is_object($value) && !method_exists($value, '__toString')) {
			if($value instanceof \DateTime) {
				return $this->formatDateTime($value);
			} else {
				return $force ? sprintf('[OBJECT:%s]', get_class($value)) : false;
			}
		}
		if(!settype($value, 'string')) {
			return $force ? '' : false;
		}
		return $value;
	}

	protected function stringifyArray(array $array, $force = true) {
		$parts = [];
		foreach($array as $part) {
			$part = $this->stringify($part, false);
			if(!is_string($part)) {
				return $force ? '[ARRAY]' : false;
			}
			$parts[] = $part;
		}
		return implode(', ', $parts);
	}

	protected function formatDateTime(\DateTime $dateTime) {
		return $dateTime->format('Y-m-d H:i:s');
	}

}