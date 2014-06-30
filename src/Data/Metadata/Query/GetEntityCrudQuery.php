<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class GetEntityCrudQuery implements QueryInterface {

	protected $reader;

	protected $annotationClass = 'Sentient\\Data\\Metadata\\Annotation\\CrudEntity';

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'getEntityCrud';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$annotation = $this->reader->getClassAnnotation($classMetadata->getReflectionClass(), $this->annotationClass);
		if(is_a($annotation, $this->annotationClass)) {
			return $annotation;
		}
		return $this->getFallbackResult($classMetadata, $options);
	}

	protected function getFallbackResult(ClassMetadata $classMetadata, array $options) {
		$reflClass = new \ReflectionClass($this->annotationClass);
		return $reflClass->newInstance([]);
	}

}