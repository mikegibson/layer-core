<?php

namespace Layer\Data\Paginator;

interface TableDataDecoratorInterface {

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return mixed
	 */
	public function decorate($value, $key, $object);

}