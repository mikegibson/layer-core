<?php

namespace Layer\Cms\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\Query\GetEditablePropertiesQuery;
use Layer\Data\Metadata\QueryInterface;

class GetCmsFormFieldsQuery implements QueryInterface {

	protected $editablePropertiesQuery;

	protected $formFieldPropertyQuery;

	public function __construct(
		GetEditablePropertiesQuery $editablePropertiesQuery,
		GetCmsFormFieldPropertyQuery $formFieldPropertyQuery
	) {
		$this->editablePropertiesQuery = $editablePropertiesQuery;
		$this->formFieldPropertyQuery = $formFieldPropertyQuery;
	}

	public function getName() {
		return 'getCmsFormFields';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$create = !isset($options['create']) || $options['create'];
		$editableProperties = $this->editablePropertiesQuery->getResult($classMetadata, compact('create'));
		$fields = [];
		foreach($editableProperties as $property) {
			$fields[$property] = $this->formFieldPropertyQuery->getResult($classMetadata, compact('property'));
		}
		return $fields;
	}

}