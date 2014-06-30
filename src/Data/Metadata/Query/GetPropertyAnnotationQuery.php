<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class GetPropertyAnnotationQuery implements QueryInterface {

	private $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'getPropertyAnnotation';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		foreach(['property', 'annotationClass'] as $key) {
			if(!isset($options[$key])) {
				throw new \InvalidArgumentException(sprintf('The %s option was not specified.', $key));
			}
		}
		$options = array_merge(['checkGetter' => true, 'checkSetter' => true], $options);
		$reflClass = $classMetadata->getReflectionClass();
		$annotation = null;
		if($reflClass->hasProperty($options['property'])) {
			$property = $classMetadata->getReflectionClass()->getProperty($options['property']);
			$annotation = $this->reader->getPropertyAnnotation($property, $options['annotationClass']);
		}
		if($annotation === null) {
			$methods = [];
			if($options['checkGetter']) {
				$methods[] = 'get' . ucfirst($options['property']);
			}
			if($options['checkSetter']) {
				$methods[] = 'set' . ucfirst($options['property']);
			}
			foreach($methods as $name) {
				if(!$reflClass->hasMethod($name)) {
					continue;
				}
				$method = $reflClass->getMethod($name);
				if(!$method->isPublic()) {
					continue;
				}
				$annotation = $this->reader->getMethodAnnotation($method, $options['annotationClass']);
				if($annotation !== null) {
					break;
				}
			}
		}
		return $annotation;
	}

}