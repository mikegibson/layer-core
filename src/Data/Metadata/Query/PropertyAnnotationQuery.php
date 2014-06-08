<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

abstract class PropertyAnnotationQuery implements QueryInterface {

	private $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
		$this->initialize();
	}

	protected function initialize() {}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$this->checkProperty($options);
		$property = $classMetadata->getReflectionClass()->getProperty($options['property']);
		$annotation = $this->getReader()->getPropertyAnnotation($property, $this->getAnnotationClass());
		return $this->getResultFromAnnotation($classMetadata, $annotation, $options);
	}

	protected function getReader() {
		return $this->reader;
	}

	protected function checkProperty(array $options) {
		if(!isset($options['property'])) {
			throw new \InvalidArgumentException('The property option must be specified!');
		}
	}

	abstract protected function getAnnotationClass();

	abstract protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options);

}