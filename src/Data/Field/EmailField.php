<?php

namespace Layer\Data\Field;

use Symfony\Component\Validator\Constraints\Email;

/**
 * Class EmailField
 * @package Layer\Data\Field
 */
class EmailField extends StringField {

	public function getConstraints() {
		$constraints = parent::getConstraints();
		$constraints[] = new Email();
		return $constraints;
	}

}