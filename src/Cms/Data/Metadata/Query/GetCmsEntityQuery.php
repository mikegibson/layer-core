<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;

class GetCmsEntityQuery implements QueryInterface {

	protected $reader;

	protected $annotationClass = 'Sentient\\Cms\\Data\\Metadata\\Annotation\\CmsEntity';

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'getCmsEntity';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		return $this->reader->getClassAnnotation($classMetadata->getReflectionClass(), $this->annotationClass);
	}

}