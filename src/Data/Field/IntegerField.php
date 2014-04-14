<?php

namespace Layer\Data\Field;

/**
 * Class IntegerField
 * @package Layer\Data\Field
 */
class IntegerField extends Field {

	/**
	 * @var string
	 */
	public $type = 'integer';

	public $htmlSafe = true;

	/**
	 * @param ValidationSet $set
	 * @return ValidationSet
	 */
	public function validation(ValidationSet $set) {

		$set = parent::validation($set);
		$set->add('validFormat', [
			'rule' => 'numeric',
			'message' => 'The number must be valid'
		]);

		return $set;
	}

}