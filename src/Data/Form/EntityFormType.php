<?php

namespace Layer\Data\Form;

use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityFormType extends AbstractType {

	protected $repository;

	protected $isCreate;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param bool $isCreate
	 */
	public function __construct(ManagedRepositoryInterface $repository, $isCreate = false) {
		$this->repository = $repository;
		$this->isCreate = $isCreate;
	}

	public function getName() {
		return $this->repository->getBasename();
	}

	public function getRepository() {
		return $this->repository;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$properties = $this->repository->getPropertyMetadata(null, "Layer\\Data\\Metadata\\Crud\\EditableField");
		foreach($properties as $field => $info) {
			if(($this->isCreate && !$info->onCreate) || (!$this->isCreate && !$info->onUpdate)) {
				continue;
			}
			$builder->add($field, $info->inputType, []);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => $this->repository->getClassName()
		));
	}

}