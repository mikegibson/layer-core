<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

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
		foreach($classMetadata->getReflectionClass()->getMethods(\ReflectionMethod::IS_PUBLIC) as $reflMethod) {
			if(!preg_match('/^set([A-Z][A-Za-z0-9]+)$/', $reflMethod->getName(), $matches)) {
				continue;
			}
			$property = lcfirst($matches[1]);
			if($this->isPropertyEditableQuery->getResult($classMetadata, compact('property', 'create'))) {
				$editableProperties[] = $property;
			}
		}
		return $editableProperties;
	}

}