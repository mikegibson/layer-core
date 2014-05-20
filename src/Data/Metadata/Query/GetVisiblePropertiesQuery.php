<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class GetVisiblePropertiesQuery implements QueryInterface {

	protected $isPropertyVisibleQuery;

	public function __construct(IsPropertyVisibleQuery $isPropertyVisibleQuery) {
		$this->isPropertyVisibleQuery = $isPropertyVisibleQuery;
	}

	public function getName() {
		return 'getVisibleProperties';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$important = !empty($options['important']);
		$visibleProperties = [];
		foreach($classMetadata->getReflectionProperties() as $reflProperty) {
			$property = $reflProperty->getName();
			if($this->isPropertyVisibleQuery->getResult($classMetadata, compact('property', 'important'))) {
				$visibleProperties[] = $property;
			}
		}
		return $visibleProperties;
	}

}