<?php

namespace Layer\Data\Field;

/**
 * Class DecimalField
 *
 * @package Layer\DataScaffold\DataType\Field
 * @todo Add validation
 */
class DecimalField extends Field {

    /**
     * @var string
     */
    public $type = 'decimal';

    public $htmlSafe = true;

}