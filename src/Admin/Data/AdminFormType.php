<?php

namespace Layer\Admin\Data;

use Layer\Data\DataType;
use Layer\Data\DataTypeFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminFormType extends AbstractType {

	/**
	 * @var \Layer\Data\DataType
	 */
	private $dataType;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @param DataType $dataType
	 * @param $name
	 */
	public function __construct(DataType $dataType, $name) {
		$this->dataType = $dataType;
		$this->name = $name;
	}

	/**
	 * @return DataType
	 */
	public function getDataType() {
		return $this->dataType;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('record', new DataTypeFormType($this->dataType), ['label' => false]);
		$builder->add('save', 'submit', ['label' => 'Save and edit']);
		$builder->add('save_and_add', 'submit', ['label' => 'Save and add another']);
	}

}