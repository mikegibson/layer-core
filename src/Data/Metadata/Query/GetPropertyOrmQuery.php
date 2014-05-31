<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class GetPropertyOrmQuery extends PropertyAnnotationQuery {

	public function getName() {
		return 'getPropertyOrm';
	}

	protected function getAnnotationClass() {
		return 'Doctrine\\ORM\\Mapping\\Column';
	}

	protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options) {
		$class = $this->getAnnotationClass();
		if(!is_a($annotation, $class)) {
			$annotation = new $class([]);
		}
		return $annotation;
	}

}