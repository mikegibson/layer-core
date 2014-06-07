<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Query\GetPropertyOrmQuery;
use Layer\Data\Metadata\Query\PropertyAnnotationQuery;

class GetCmsFormFieldPropertyQuery extends PropertyAnnotationQuery {

	protected $propertyOrmQuery;

	protected $typeMap = [
	//	'text' => 'textarea'
	];

	public function __construct(Reader $reader, GetPropertyOrmQuery $propertyOrmQuery) {
		parent::__construct($reader);
		$this->propertyOrmQuery = $propertyOrmQuery;
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
		if($type === null) {
			$orm = $this->propertyOrmQuery->getResult($classMetadata, ['property' => $options['property']]);
			if(isset($this->typeMap[$orm->type])) {
				$type = $this->typeMap[$orm->type];
			}
		}
		return [
			'type' => $type,
			'options' => $fieldOptions
		];
	}

}