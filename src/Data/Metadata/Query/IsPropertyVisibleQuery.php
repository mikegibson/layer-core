<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IsPropertyVisibleQuery implements QueryInterface {

	/**
	 * @var GetPropertyAnnotationQuery
	 */
	private $annotationQuery;

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @param GetPropertyAnnotationQuery $annotationQuery
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(GetPropertyAnnotationQuery $annotationQuery, PropertyAccessorInterface $propertyAccessor) {
		$this->annotationQuery = $annotationQuery;
		$this->propertyAccessor = $propertyAccessor;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'isPropertyVisible';
	}

	/**
	 * @param ClassMetadata $classMetadata
	 * @param array $options
	 * @return bool
	 * @throws \InvalidArgumentException
	 */
	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(!isset($options['property'])) {
			throw new \InvalidArgumentException('The property option must be specified!');
		}
		if(!$this->propertyAccessor->isReadable($classMetadata->newInstance(), $options['property'])) {
			return false;
		}
		if($annotation = $this->annotationQuery->getResult($classMetadata, [
			'property' => $options['property'],
			'annotationClass' => 'Layer\\Data\\Metadata\\Annotation\\InvisibleProperty',
			'checkSetter' => false
		])) {
			return false;
		}
		if($annotation = $this->annotationQuery->getResult($classMetadata, [
			'property' => $options['property'],
			'annotationClass' => 'Layer\\Data\\Metadata\\Annotation\\CrudProperty',
			'checkSetter' => false
		])) {
			if(!empty($options['important'])) {
				return !empty($annotation->important);
			}
			return !empty($annotation->visible);
		}
		return true;
	}

}