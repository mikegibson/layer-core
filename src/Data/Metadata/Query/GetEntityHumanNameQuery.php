<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;
use Sentient\Utility\InflectorInterface;

class GetEntityHumanNameQuery implements QueryInterface {

	protected $reader;

	protected $inflector;

	protected $annotationClass = 'Sentient\\Data\\Metadata\\Annotation\\EntityHumanName';

	public function __construct(Reader $reader, InflectorInterface $inflector) {
		$this->reader = $reader;
		$this->inflector = $inflector;
	}

	public function getName() {
		return 'getEntityHumanName';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$annotation = $this->reader->getClassAnnotation($classMetadata->getReflectionClass(), $this->annotationClass);
		if(!is_a($annotation, $this->annotationClass)) {
			$humanized = $this->getFallbackResult($classMetadata, $options);
		} else {
			$humanized = empty($options['plural']) ? $annotation->singular : $annotation->plural;
		}
		if(!empty($options['capitalize'])) {
			$humanized = $this->capitalize($humanized);
		}
		return $humanized;
	}

	protected function getFallbackResult(ClassMetadata $classMetadata, array $options) {
		$parts = explode('\\', $classMetadata->getName());
		$className = array_pop($parts);
		$humanized = $this->inflector->humanize($this->inflector->underscore($className));
		if(!empty($options['plural'])) {
			$humanized = $this->inflector->pluralize($humanized);
		}
		return $humanized;
	}

	protected function capitalize($humanized) {
		return ucfirst($humanized);
	}

}