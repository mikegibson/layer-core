<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;
use Layer\Utility\InflectorInterface;

class GetEntityHumanNameQuery implements QueryInterface {

	protected $reader;

	protected $inflector;

	protected $annotationClass = 'Layer\\Data\\Metadata\\Annotation\\EntityHumanName';

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
			return $this->getFallbackResult($classMetadata, $options);
		}
		return empty($options['plural']) ? $annotation->singular : $annotation->plural;
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

}