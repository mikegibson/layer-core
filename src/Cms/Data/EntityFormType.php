<?php

namespace Layer\Cms\Data;

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
		return $this->getRepository()->getName();
	}

	public function getRepository() {
		return $this->repository;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$fields = $this->getRepository()->queryMetadata('getCmsFormFields', ['create' => $this->isCreate]);
		foreach($fields as $field => $info) {
			$builder->add($field, $info['type'], $info['options']);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => $this->getRepository()->getClassName()
		));
	}

}