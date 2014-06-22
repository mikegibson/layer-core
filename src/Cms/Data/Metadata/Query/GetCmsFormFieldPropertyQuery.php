<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Query\GetPropertyAnnotationQuery;
use Layer\Data\Metadata\Query\GetPropertyLabelQuery;
use Layer\Data\Metadata\QueryInterface;

class GetCmsFormFieldPropertyQuery implements QueryInterface {

	/**
	 * @var \Layer\Data\Metadata\Query\GetPropertyAnnotationQuery
	 */
	private $annotationQuery;

	/**
	 * @var \Layer\Data\Metadata\Query\GetPropertyLabelQuery
	 */
	private $labelQuery;

	protected $annotationClass = 'Layer\\Cms\\Data\\Metadata\\Annotation\\FormFieldProperty';

	/**
	 * @param GetPropertyAnnotationQuery $annotationQuery
	 * @param GetPropertyLabelQuery $propertyLabelQuery
	 */
	public function __construct(
		GetPropertyAnnotationQuery $annotationQuery,
		GetPropertyLabelQuery $propertyLabelQuery
	) {
		$this->annotationQuery = $annotationQuery;
		$this->labelQuery = $propertyLabelQuery;
	}

	public function getName() {
		return 'getCmsFormFieldProperty';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(!isset($options['property'])) {
			throw new \InvalidArgumentException('The property option was not specified.');
		}
		if(!$annotation = $this->annotationQuery->getResult($classMetadata, [
			'property' => $options['property'],
			'annotationClass' => $this->annotationClass
		])) {
			$class = $this->annotationClass;
			$annotation = new $class([]);
		}
		$type = $annotation->type;
		if(empty($type)) {
			if($this->annotationQuery->getResult($classMetadata, [
				'property' => $options['property'],
				'annotationClass' => 'Layer\\Data\\Metadata\\Annotation\\HtmlProperty'
			])) {
				$type = 'html';
			}
		}
		$fieldOptions = $annotation->options;
		if(!isset($fieldOptions['label'])) {
			$fieldOptions['label'] = $this->labelQuery->getResult($classMetadata, ['property' => $options['property']]);
		}
		return [
			'type' => $type,
			'options' => $fieldOptions
		];
	}

}