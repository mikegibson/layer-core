<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class IsHtmlPropertyQuery implements QueryInterface {

	private $annotationQuery;

	protected $annotationClass = 'Layer\\Data\\Metadata\\Annotation\\HtmlProperty';

	public function __construct(GetPropertyAnnotationQuery $annotationQuery) {
		$this->annotationQuery = $annotationQuery;
	}

	public function getName() {
		return 'isHtmlProperty';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$options['annotationClass'] = $this->annotationClass;
		return !!$this->annotationQuery->getResult($classMetadata, $options);
	}

}