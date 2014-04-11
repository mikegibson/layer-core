<?php

namespace Layer\Data\Field;

/**
 * Class TimestampField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class TimestampField extends DatetimeField {

    /**
     * @var string
     */
    public $type = 'timestamp';

    public $htmlSafe = true;

}