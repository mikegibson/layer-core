<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractType;

class HtmlType extends AbstractType {

	public function getName() {
		return 'html';
	}

	public function getParent() {
		return 'text';
	}

}