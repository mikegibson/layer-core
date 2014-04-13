<?php

namespace Layer\Data\Field;

	//use Cake\Validation\ValidationSet;

/**
 * Class BooleanField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class BooleanField extends Field {

	/**
	 * @var string
	 */
	public $type = 'boolean';

	/**
	 * @param ValidationSet $set
	 * @return ValidationSet
	 * /
	 * public function validation(ValidationSet $set) {
	 * $set = parent::validation($set);
	 * $set->add('validFormat', [
	 * 'rule' => 'boolean',
	 * 'message' => 'The value must be true or false'
	 * ]);
	 * return $set;
	 * }*/

}