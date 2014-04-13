<?php

namespace Layer\Data\Field;

	//use Cake\Validation\ValidationSet;

/**
 * Class DatetimeField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class DatetimeField extends Field {

	/**
	 * @var string
	 */
	public $type = 'datetime';

	public $htmlSafe = true;

	/**
	 * @param ValidationSet $set
	 * @return ValidationSet
	 * /
	 * public function validation(ValidationSet $set) {
	 * $set = parent::validation($set);
	 * $set->add('validFormat', [
	 * 'rule' => 'datetime',
	 * 'message' => 'The date and time must be valid'
	 * ]);
	 * return $set;
	 * }*/

}