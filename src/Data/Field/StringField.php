<?php

namespace Layer\Data\Field;

/**
 * Class StringField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class StringField extends Field {

    /**
     * @var string
     */
    public $type = 'string';

    /**
     * @var int
     */
    public $length = 200;

    /**
     * @var bool
     */
    public $fixed = false;

}