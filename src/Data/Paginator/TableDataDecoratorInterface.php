<?php

namespace Layer\Data\Paginator;

interface TableDataDecoratorInterface {

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return string
	 */
	public function decorate($value, $key, $object);

}