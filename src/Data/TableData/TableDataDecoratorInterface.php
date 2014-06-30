<?php

namespace Sentient\Data\TableData;

interface TableDataDecoratorInterface {

	/**
	 * @param array $columns
	 * @return mixed
	 */
	public function decorateColumns(array $columns);

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return string
	 */
	public function decorateData($value, $key, $object);

}