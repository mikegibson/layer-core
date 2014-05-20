<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class IsPropertyEditableQuery implements QueryInterface {

	protected $bannedAnnotations = [
		'Doctrine\\ORM\\Mapping\\Id',
		'Doctrine\\ORM\\Mapping\\GeneratedValue',
		'Gedmo\\Mapping\\Annotation\\Timestampable',
		'Layer\\Data\\Metadata\\Annotation\\InvisibleProperty'
	];

	/**
	 * @var \Doctrine\Common\Annotations\Reader
	 */
	protected $reader;

	/**
	 * @param Reader $reader
	 */
	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	public function getName() {
		return 'isPropertyEditable';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(!isset($options['property'])) {
			throw new \InvalidArgumentException('The property option must be specified!');
		}
		$property = $classMetadata->getReflectionProperty($options['property']);
		$create = !isset($options['create']) || $options['create'];
		if($annotation = $this->reader->getPropertyAnnotation($property, 'Layer\\Data\\Metadata\\Annotation\\LockedProperty')) {
			if($create ? $annotation->onCreate : $annotation->onUpdate) {
				return false;
			}
		}
		if($annotation = $this->reader->getPropertyAnnotation($property, 'Layer\\Data\\Metadata\\Annotation\\CrudProperty')) {
			if(!($create ? $annotation->create : $annotation->update)) {
				return false;
			}
		}
		foreach($this->bannedAnnotations as $annotationName) {
			if($this->reader->getPropertyAnnotation($property, $annotationName)) {
				return false;
			}
		}
		return true;
	}

}