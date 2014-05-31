<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class GetCmsEntityQuery implements QueryInterface {

	protected $reader;

	protected $annotationClass = 'Layer\\Cms\\Data\\Metadata\\Annotation\\CmsEntity';

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