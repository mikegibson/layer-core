<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Query\GetPropertyLabelQuery;
use Layer\Data\Metadata\Query\IsHtmlPropertyQuery;
use Layer\Data\Metadata\Query\PropertyAnnotationQuery;

class GetCmsFormFieldPropertyQuery extends PropertyAnnotationQuery {

	/**
	 * @var \Layer\Data\Metadata\Query\GetPropertyLabelQuery
	 */
	protected $propertyLabelQuery;

	/**
	 * @var \Layer\Data\Metadata\Query\IsHtmlPropertyQuery
	 */
	protected $htmlPropertyQuery;

	/**
	 * @param Reader $reader
	 * @param GetPropertyLabelQuery $propertyLabelQuery
	 */
	public function __construct(
		Reader $reader,
		GetPropertyLabelQuery $propertyLabelQuery,
		IsHtmlPropertyQuery $htmlPropertyQuery
	) {
		parent::__construct($reader);
		$this->propertyLabelQuery = $propertyLabelQuery;
		$this->htmlPropertyQuery = $htmlPropertyQuery;
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
		if(empty($type)) {
			if($this->htmlPropertyQuery->getResult($classMetadata, ['property' => $options['property']])) {
				$type = 'html';
			}
		}
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