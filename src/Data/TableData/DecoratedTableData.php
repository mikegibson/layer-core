<?php

namespace Layer\Data\TableData;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DecoratedTableData implements TableDataInterface {

	/**
	 * @var TableDataInterface
	 */
	private $baseTableData;

	/**
	 * @var TableDataDecoratorInterface
	 */
	private $decorator;

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @param TableDataInterface $baseTableData
	 * @param TableDataDecoratorInterface $decorator
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(
		TableDataInterface $baseTableData,
		TableDataDecoratorInterface $decorator,
		PropertyAccessorInterface $propertyAccessor
	) {
		$this->baseTableData = $baseTableData;
		$this->decorator = $decorator;
		$this->propertyAccessor = $propertyAccessor;
	}

	/**
	 * @return array
	 */
	public function getColumns() {
		return $this->baseTableData->getColumns();
	}

	/**
	 * @return array
	 */
	public function getData() {
		$data = [];
		foreach($this->baseTableData->getData() as $baseRow) {
			$row = [];
			foreach($this->getColumns() as $column => $label) {
				$value = $this->propertyAccessor->getValue($baseRow, $column);
				$row[$column] = $this->decorator->decorateData($value, $column, $baseRow);
			}
			$data[] = $row;
		}
		return $data;
	}

}