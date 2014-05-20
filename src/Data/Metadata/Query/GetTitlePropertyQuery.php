<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class GetTitlePropertyQuery implements QueryInterface {

	protected $isTitlePropertyQuery;

	public function __construct(IsTitlePropertyQuery $isTitlePropertyQuery) {
		$this->isTitlePropertyQuery = $isTitlePropertyQuery;
	}

	public function getName() {
		return 'getTitleProperty';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		foreach([true, false] as $strict) {
			foreach($classMetadata->getReflectionProperties() as $reflProperty) {
				$property = $reflProperty->getName();
				if($this->isTitlePropertyQuery->getResult($classMetadata, compact('property', 'strict'))) {
					return $property;
				}
			}
		}
		return null;
	}

}