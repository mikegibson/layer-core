<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

abstract class PropertyAnnotationQuery implements QueryInterface {

	protected $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
		$this->initialize();
	}

	protected function initialize() {}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(!isset($options['property'])) {
			throw new \InvalidArgumentException('The property option must be specified!');
		}
		$property = $classMetadata->getReflectionProperty($options['property']);
		$annotation = $this->reader->getPropertyAnnotation($property, $this->getAnnotationClass());
		return $this->getResultFromAnnotation($classMetadata, $annotation, $options);
	}

	abstract protected function getAnnotationClass();

	abstract protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options);

}