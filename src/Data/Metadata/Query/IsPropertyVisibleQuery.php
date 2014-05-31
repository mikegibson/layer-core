<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class IsPropertyVisibleQuery extends PropertyAnnotationQuery {

	private $namespace = 'Layer\\Data\\Metadata\\Annotation\\';

	protected function getAnnotationClass() {
		return $this->namespace . 'CrudEntity';
	}

	public function getName() {
		return 'isPropertyVisible';
	}

	protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options) {
		if(is_a($annotation, $this->getAnnotationClass())) {
			if(!empty($options['important'])) {
				return !empty($annotation->important);
			}
			return true;
		}
		return $this->getFallbackResult($classMetadata, $options);
	}

	protected function getFallbackResult(ClassMetadata $classMetadata, array $options) {
		$property = $classMetadata->getReflectionProperty($options['property']);
		if($this->reader->getPropertyAnnotation($property, $this->namespace . 'InvisibleProperty')) {
			return false;
		}
		if($crudProperty = $this->reader->getPropertyAnnotation($property, $this->namespace . 'CrudProperty')) {
			return $crudProperty->visible;
		}
		return true;
	}

}