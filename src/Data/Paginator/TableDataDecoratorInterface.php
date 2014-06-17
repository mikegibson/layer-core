<?php

namespace Layer\Data\Paginator;

interface TableDataDecoratorInterface {

	/**
	 * @param $key
	 * @param $value
	 * @return string
	 */
	public function decorate($value, $key, $object);

}