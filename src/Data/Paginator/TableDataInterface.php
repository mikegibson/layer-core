<?php

namespace Layer\Data\Paginator;

interface TableDataInterface {

	/**
	 * @return array
	 */
	public function getColumns();

	/**
	 * @return array
	 */
	public function getData();

}