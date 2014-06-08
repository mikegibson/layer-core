<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class GetEditablePropertiesQuery implements QueryInterface {

	protected $isPropertyEditableQuery;

	public function __construct(IsPropertyEditableQuery $isPropertyEditableQuery) {
		$this->isPropertyEditableQuery = $isPropertyEditableQuery;
	}

	public function getName() {
		return 'getEditableProperties';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$create = !isset($options['create']) || $options['create'];
		$editableProperties = [];
		foreach($classMetadata->getReflectionClass()->getProperties() as $reflProperty) {
			$property = $reflProperty->getName();
			if($this->isPropertyEditableQuery->getResult($classMetadata, compact('property', 'create'))) {
				$editableProperties[] = $property;
			}
		}
		return $editableProperties;
	}

}