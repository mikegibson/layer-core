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
		$properties = $this->getRepository()->queryMetadata('getEditableProperties', ['create' => $this->isCreate]);
		foreach($properties as $property) {
			$type = null;
			$builderOptions = [
				'label' => $this->getRepository()->queryMetadata('getPropertyLabel', compact('property'))
			];
			if($annotation = $this->getRepository()->queryMetadata('getPropertyAnnotation', [
				'property' => $property,
				'annotationClass' => 'Layer\\Cms\\Data\\Metadata\\Annotation\\FormFieldProperty'
			])) {
				if(!empty($annotation->value)) {
					$type = $annotation->value;
				}
				$builderOptions = array_merge($builderOptions, $annotation->options);
			}
			$builder->add($property, $type, $builderOptions);
		}
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => $this->getRepository()->getClassName()
		));
	}

}