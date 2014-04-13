<?php

namespace Layer\Data\Field;

	//use Cake\Validation\ValidationSet;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Class EmailField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class EmailField extends StringField {

	public function getConstraints() {
		$constraints = parent::getConstraints();
		$constraints[] = new Email();
		return $constraints;
	}

}