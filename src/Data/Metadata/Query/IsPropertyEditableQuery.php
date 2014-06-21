<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Annotation\CrudProperty;
use Layer\Data\Metadata\QueryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	protected $propertyAccessor;

	/**
	 * @param Reader $reader
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(Reader $reader, PropertyAccessorInterface $propertyAccessor) {
		$this->reader = $reader;
		$this->propertyAccessor = $propertyAccessor;
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
		$create = !isset($options['create']) || $options['create'];
		$reflection = $classMetadata->getReflectionClass();
		$instance = $classMetadata->newInstance();
		if(
			!$this->propertyAccessor->isWritable($instance, $options['property']) ||
			!$this->propertyAccessor->isReadable($instance, $options['property'])
		) {
			return false;
		}
		$property = $reflection->getProperty($options['property']);
		if($annotation = $this->reader->getPropertyAnnotation($property, 'Layer\\Data\\Metadata\\Annotation\\LockedProperty')) {
			if($create ? $annotation->onCreate : $annotation->onUpdate) {
				return false;
			}
		}
		if($annotation = $this->reader->getPropertyAnnotation($property, 'Layer\\Data\\Metadata\\Annotation\\CrudProperty')) {
			if($annotation->editable === CrudProperty::EDITABLE_ALWAYS) {
				return true;
			}
			if($annotation->editable === CrudProperty::EDITABLE_NEVER) {
				return false;
			}
			if($annotation->editable === CrudProperty::EDITABLE_ON_CREATE) {
				return $create;
			}
			if($annotation->editable === CrudProperty::EDITABLE_ON_UPDATE) {
				return !$create;
			}
			throw new \RuntimeException('The editable property has an invalid value.');
		}
		foreach($this->bannedAnnotations as $annotationName) {
			if($this->reader->getPropertyAnnotation($property, $annotationName)) {
				return false;
			}
		}
		return true;
	}

}