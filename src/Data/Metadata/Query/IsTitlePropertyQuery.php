<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class IsTitlePropertyQuery extends PropertyAnnotationQuery {

	protected $titleProperties = [
		'title', 'name'
	];

	public function getName() {
		return 'isTitleProperty';
	}

	protected function getAnnotationClass() {
		return 'Layer\\Data\\Metadata\\Annotation\\TitleProperty';
	}

	protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options) {
		if(is_a($annotation, $this->annotationClass)) {
			return true;
		}
		if(!empty($options['strict'])) {
			return false;
		}
		return in_array($options['property'], $this->titleProperties);
	}

}