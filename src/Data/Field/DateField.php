<?php

namespace Layer\Data\Field;

//use Cake\Validation\ValidationSet;

/**
 * Class DateField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class DateField extends Field {

    /**
     * @var string
     */
    public $type = 'date';

    public $htmlSafe = true;

    /**
     * @param ValidationSet $set
     * @return ValidationSet
     * /
     * public function validation(ValidationSet $set) {
     * $set = parent::validation($set);
     * $set->add('validFormat', [
     * 'rule' => 'date',
     * 'message' => 'The date must be valid'
     * ]);
     * return $set;
     * }*/

}