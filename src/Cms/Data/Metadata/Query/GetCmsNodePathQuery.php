<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class GetCmsNodePathQuery implements QueryInterface {

	protected $annotationClass = 'Sentient\\Cms\\Data\\Metadata\\Annotation\\RootNodePath';

	private $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'getCmsNodePath';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$annotation = $this->reader->getClassAnnotation($classMetadata->getReflectionClass(), $this->annotationClass);
		return !empty($annotation->value) ? $annotation->value : null;
	}

}