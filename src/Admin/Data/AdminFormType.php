<?php

namespace Layer\Admin\Data;

use Layer\Data\EntityFormType;
use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminFormType extends AbstractType {

	/**
	 * @var \Layer\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param $name
	 */
	public function __construct(ManagedRepositoryInterface $repository, $name) {
		$this->repository = $repository;
		$this->name = $name;
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	public function getRepository() {
		return $this->repository;
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
		$builder->add('record', new EntityFormType($this->repository), ['label' => false]);
		$builder->add('save', 'submit', ['label' => 'Save and edit']);
		$builder->add('save_and_add', 'submit', ['label' => 'Save and add another']);
	}

}