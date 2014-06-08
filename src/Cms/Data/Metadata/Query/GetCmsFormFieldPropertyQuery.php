<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Query\GetPropertyLabelQuery;
use Layer\Data\Metadata\Query\PropertyAnnotationQuery;

class GetCmsFormFieldPropertyQuery extends PropertyAnnotationQuery {

	/**
	 * @var \Layer\Data\Metadata\Query\GetPropertyLabelQuery
	 */
	protected $propertyLabelQuery;

	/**
	 * @param Reader $reader
	 * @param GetPropertyLabelQuery $propertyLabelQuery
	 */
	public function __construct(
		Reader $reader,
		GetPropertyLabelQuery $propertyLabelQuery)
	{
		parent::__construct($reader);
		$this->propertyLabelQuery = $propertyLabelQuery;
	}

	public function getName() {
		return 'getCmsFormFieldProperty';
	}

	protected function getAnnotationClass() {
		return 'Layer\\Cms\\Data\\Metadata\\Annotation\\FormFieldProperty';
	}

	protected function getResultFromAnnotation(ClassMetadata $classMetadata, $annotation, array $options) {
		$class = $this->getAnnotationClass();
		if(!is_a($annotation, $class)) {
			$annotation = new $class([]);
		}
		$type = $annotation->type;
		$fieldOptions = $annotation->options;
		if(!isset($fieldOptions['label'])) {
			$fieldOptions['label'] = $this->propertyLabelQuery->getResult($classMetadata, ['property' => $options['property']]);
		}
		return [
			'type' => $type,
			'options' => $fieldOptions
		];
	}

}