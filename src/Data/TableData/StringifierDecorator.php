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
		if(is_array($value)) {
			$value = '[ARRAY]';
		} elseif(is_object($value) && !method_exists($value, '__toString')) {
			if($value instanceof \DateTime) {
				$value = $this->formatDateTime($value);
			} else {
				$value = sprintf('[OBJECT:%s]', get_class($value));
			}
		}
		if(!settype($value, 'string')) {
			return '';
		}
		return $value;
	}

	protected function formatDateTime(\DateTime $dateTime) {
		return $dateTime->format('Y-m-d H:i:s');
	}

}