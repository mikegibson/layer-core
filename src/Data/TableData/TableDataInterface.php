<?php

namespace Sentient\Data\TableData;

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