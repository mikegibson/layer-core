<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class IsHtmlPropertyQuery extends PropertyAnnotationQuery {

	public function getName() {
		return 'isHtmlProperty';
	}

	protected function getAnnotationClass() {
		return 'Layer\\Data\\Metadata\\Annotation\\HtmlProperty';
	}

	protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options) {
		return is_a($annotation, $this->getAnnotationClass());
	}

}