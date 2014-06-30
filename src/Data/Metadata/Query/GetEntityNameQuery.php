<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class GetEntityNameQuery implements QueryInterface {

	protected $reader;

	protected $annotationClass = 'Sentient\\Data\\Metadata\\Annotation\\EntityName';

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'getEntityName';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$reflClass = $classMetadata->getReflectionClass();
		$annotation = $this->reader->getClassAnnotation($reflClass, $this->annotationClass);
		if(!empty($annotation->value)) {
			return $annotation->value;
		}
		return $this->getFallbackResult($classMetadata, $options);
	}

	protected function getFallbackResult(ClassMetadata $classMetadata, array $options) {
		$parts = explode('\\', $classMetadata->getName());
		return array_pop($parts);
	}

}