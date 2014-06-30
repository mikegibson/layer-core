<?php

namespace Sentient\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FormTypeExtension extends AbstractTypeExtension {

	public function finishView(FormView $view, FormInterface $form, array $options) {
		$view->vars['formType'] = $form->getConfig()->getType()->getName();
	}

	public function getExtendedType() {
		return 'form';
	}

}