<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateTimeTypeExtension extends AbstractTypeExtension {

	/**
	 * {@inheritdoc}
	 */
	public function getExtendedType() {
		return 'datetime';
	}

	/**
	 * {@inheritdoc}
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults([
			'date_widget' => 'single_text',
			'date_format' => DateType::HTML5_FORMAT,
			'time_widget' => 'single_text'
		]);
	}

}