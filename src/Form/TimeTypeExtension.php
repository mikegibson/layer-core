<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeTypeExtension extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType() {
		return 'time';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'widget' => 'single_text'
		]);
	}

}