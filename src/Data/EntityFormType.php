<?php

namespace Layer\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityFormType extends AbstractType {

	private $repository;

	protected $typeMap = [
		'text' => 'textarea'
	];

	public function __construct(ManagedRepositoryInterface $repository) {
		$this->repository = $repository;
	}

	public function getName() {
		return $this->repository->getBasename();
	}

	public function getRepository() {
		return $this->repository;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$metadata = $this->repository->getEntityMetadata();
		foreach($this->repository->getEditableFields() as $field) {
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
			'data_class' => $this->repository->getClassName()
		));
	}

}