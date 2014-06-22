<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTypeExtension extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType() {
		return 'date';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'widget' => 'single_text',
			'format' => DateType::HTML5_FORMAT
		]);
	}

}