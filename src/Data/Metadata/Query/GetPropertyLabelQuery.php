<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;
use Sentient\Utility\InflectorInterface;

class GetPropertyLabelQuery implements QueryInterface {

	private $annotationQuery;

	private $inflector;

	protected $annotationClass = 'Sentient\\Data\\Metadata\\Annotation\\PropertyLabel';

	public function __construct(GetPropertyAnnotationQuery $annotationQuery, InflectorInterface $inflector) {
		$this->annotationQuery = $annotationQuery;
		$this->inflector = $inflector;
	}

	public function getName() {
		return 'getPropertyLabel';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$subQueryOptions = array_intersect_key($options, array_flip(['property', 'checkGetter', 'checkSetter']));
		$subQueryOptions['annotationClass'] = $this->annotationClass;
		$annotation = $this->annotationQuery->getResult($classMetadata, $subQueryOptions);
		if(!empty($annotation->value)) {
			return $annotation->value;
		}
		return ucfirst($this->getInflector()->humanize($this->getInflector()->underscore($options['property'])));
	}

	protected function getInflector() {
		return $this->inflector;
	}

}