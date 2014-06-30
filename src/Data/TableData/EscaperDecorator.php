<?php

namespace Sentient\Data\TableData;

class EscaperDecorator implements TableDataDecoratorInterface {

	public function decorateColumns(array $columns) {
		return $columns;
	}

	public function decorateData($value, $key, $object) {
		return htmlspecialchars($value);
	}

}