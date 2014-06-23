<?php

namespace Layer\Data\TableData;

class ChainedTableDataDecorator implements TableDataDecoratorInterface {

	private $decorators = [];

	public function __construct(array $decorators = []) {
		foreach($decorators as $decorator) {
			$this->addDecorator($decorator);
		}
	}

	public function addDecorator(TableDataDecoratorInterface $decorator) {
		$this->decorators[] = $decorator;
	}

	public function decorateColumns(array $columns) {
		foreach($this->decorators as $decorator) {
			$columns = $decorator->decorateColumns($columns);
		}
		return $columns;
	}

	public function decorateData($value, $key, $object) {
		foreach($this->decorators as $decorator) {
			$value = $decorator->decorateData($value, $key, $object);
		}
		return $value;
	}

}