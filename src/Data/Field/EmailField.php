<?php

namespace Layer\Data\Field;

//use Cake\Validation\ValidationSet;

/**
 * Class EmailField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class EmailField extends StringField {

    /**
     * @param ValidationSet $set
     * @return ValidationSet
     * /
     * public function validation(ValidationSet $set) {
     * $set = parent::validation($set);
     * $set->add('validFormat', [
     * 'rule' => 'email',
     * 'message' => 'Email must be valid'
     * ]);
     * return $set;
     * }*/

}