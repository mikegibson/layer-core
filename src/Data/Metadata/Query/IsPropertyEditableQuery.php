<?php

namespace Sentient\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\Annotation\CrudProperty;
use Sentient\Data\Metadata\QueryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IsPropertyEditableQuery implements QueryInterface {

	protected $bannedAnnotations = [
		'Doctrine\\ORM\\Mapping\\Id',
		'Doctrine\\ORM\\Mapping\\GeneratedValue',
		'Gedmo\\Mapping\\Annotation\\Timestampable',
		'Sentient\\Data\\Metadata\\Annotation\\InvisibleProperty'
	];

	/**
	 * @var \Doctrine\Common\Annotations\Reader
	 */
	protected $annotationQuery;

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	protected $propertyAccessor;

	/**
	 * @param GetPropertyAnnotationQuery $annotationQuery
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(GetPropertyAnnotationQuery $annotationQuery, PropertyAccessorInterface $propertyAccessor) {
		$this->annotationQuery = $annotationQuery;
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
			throw new \InvalidArgumentException('The property option was not specified.');
		}
		$instance = $classMetadata->newInstance();
		if(
			!$this->propertyAccessor->isWritable($instance, $options['property']) ||
			!$this->propertyAccessor->isReadable($instance, $options['property'])
		) {
			return false;
		}
		if(isset($options['create'])) {
			$create = $options['create'];
			unset($options['create']);
		} else {
			$create = true;
		}
		if($annotation = $this->annotationQuery->getResult($classMetadata, array_merge($options, [
			'annotationClass' => 'Sentient\\Data\\Metadata\\Annotation\\LockedProperty'
		]))) {
			if($create ? $annotation->onCreate : $annotation->onUpdate) {
				return false;
			}
		}
		if($annotation = $this->annotationQuery->getResult($classMetadata, array_merge($options, [
			'annotationClass' => 'Sentient\\Data\\Metadata\\Annotation\\CrudProperty'
		]))) {
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
		foreach($this->bannedAnnotations as $annotationClass) {
			if($this->annotationQuery->getResult($classMetadata, array_merge($options, compact('annotationClass')))) {
				return false;
			}
		}
		return true;
	}

}