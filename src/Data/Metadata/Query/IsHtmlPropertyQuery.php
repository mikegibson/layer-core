<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class IsHtmlPropertyQuery implements QueryInterface {

	private $annotationQuery;

	protected $annotationClass = 'Sentient\\Data\\Metadata\\Annotation\\HtmlProperty';

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