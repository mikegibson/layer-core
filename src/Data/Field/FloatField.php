<?php

namespace Layer\Data\Field;

/**
 * Class FloatField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class FloatField extends Field {

	/**
	 * @var string
	 */
	public $type = 'float';

	public $htmlSafe = true;

	/**
	 * @param ValidationSet $set
	 * @return ValidationSet
	 * /
	 * public function validation(ValidationSet $set) {
	 *
	 * $set = parent::validation($set);
	 * $set->add('validFormat', [
	 * 'rule' => 'decimal',
	 * 'message' => 'The number must be valid'
	 * ]);
	 *
	 * return $set;
	 * }*/

}