<?php

namespace Layer\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DataTypeFormType extends AbstractType {

	private $dataType;

	protected $typeMap = [
		'text' => 'textarea'
	];

	public function __construct(DataType $dataType) {
		$this->dataType = $dataType;
	}

	public function getName() {
		return $this->dataType->name;
	}

	public function getDataType() {
		return $this->dataType;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$metadata = $this->dataType->getMetadata();
		foreach($this->dataType->getEditableFields() as $field) {
			$type = null;
			if(isset($metadata->fieldMappings[$field])) {
				$mapping = $metadata->fieldMappings[$field];
				if(isset($this->typeMap[$mapping['type']])) {
					$type = $this->typeMap[$mapping['type']];
				}
			}
			$builder->add($field, $type);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => $this->dataType->entityClass
		));
	}

}