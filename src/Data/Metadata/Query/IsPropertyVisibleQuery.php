<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IsPropertyVisibleQuery extends PropertyAnnotationQuery {

	private $namespace = 'Layer\\Data\\Metadata\\Annotation\\';

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	protected $propertyAccessor;

	/**
	 * @param Reader $reader
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(Reader $reader, PropertyAccessorInterface $propertyAccessor) {
		parent::__construct($reader);
		$this->propertyAccessor = $propertyAccessor;
	}

	protected function getAnnotationClass() {
		return $this->namespace . 'CrudEntity';
	}

	public function getName() {
		return 'isPropertyVisible';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$this->checkProperty($options);
		$getter = 'get' . ucfirst($options['property']);
		if(!$classMetadata->getReflectionClass()->hasMethod($getter)) {
			return false;
		}
		return parent::getResult($classMetadata, $options);
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
		$property = $classMetadata->getReflectionClass()->getProperty($options['property']);
		if($this->getReader()->getPropertyAnnotation($property, $this->namespace . 'InvisibleProperty')) {
			return false;
		}
		if($crudProperty = $this->getReader()->getPropertyAnnotation($property, $this->namespace . 'CrudProperty')) {
			return $crudProperty->visible;
		}
		if(!$this->propertyAccessor->isReadable($classMetadata->newInstance(), $options['property'])) {
			return false;
		}
		return true;
	}

}