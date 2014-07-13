<?php

namespace Sentient\Cms\Data;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface as BaseForm;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CmsEntityFormType extends AbstractType {

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var \Symfony\Component\Form\FormTypeInterface
	 */
	protected $baseForm;

	/**
	 * @param $name
	 * @param BaseForm $baseForm
	 */
	public function __construct($name, BaseForm $baseForm) {
		$this->name = $name;
		$this->baseForm = $baseForm;
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
		$builder->add('entity', $this->baseForm, ['label' => false]);
		$builder->add('save', 'submit', ['label' => 'Save and edit']);
		$builder->add('save_and_add', 'submit', ['label' => 'Save and add another']);
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'cascade_validation' => true
		]);
	}

}