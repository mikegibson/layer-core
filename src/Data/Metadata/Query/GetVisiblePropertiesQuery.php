<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

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
		foreach($classMetadata->getReflectionClass()->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
			if(!preg_match('/^get([A-Z][A-Za-z0-9]+)$/', $reflMethod->getName(), $matches)) {
				continue;
			}
			$property = lcfirst($matches[1]);
			if($this->isPropertyVisibleQuery->getResult($classMetadata, compact('property', 'important'))) {
				$visibleProperties[] = $property;
			}
		}
		return $visibleProperties;
	}

}