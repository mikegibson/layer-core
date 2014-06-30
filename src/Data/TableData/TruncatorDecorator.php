<?php

namespace Sentient\Data\TableData;

use Sentient\Utility\StringHelper;

class TruncatorDecorator implements TableDataDecoratorInterface {

	/**
	 * @var \Sentient\Utility\StringHelper
	 */
	private $stringHelper;

	/**
	 * @var int
	 */
	private $length;

	/**
	 * @param StringHelper $stringHelper
	 * @param int $length
	 */
	public function __construct(StringHelper $stringHelper, $length = 100) {
		$this->stringHelper = $stringHelper;
		$this->length = $length;
	}


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
		return $this->stringHelper->truncate($value, $this->length);
	}

}