<?php

namespace Layer\Data\Field;

/**
 * Class PrimaryKeyField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class PrimaryKeyField extends Field {

    /**
     * @var string
     */
    public $name = 'id';

    /**
     * @var string
     */
    public $label = 'ID';

    /**
     * @var string
     */
    public $type = 'integer';

    /**
     * @var bool
     */
    public $primaryKey = true;

    /**
     * @var bool
     */
    public $null = false;

    /**
     * @var bool
     */
    public $unsigned = true;

    /**
     * @var bool
     */
    public $autoIncrement = true;

    /**
     * @var bool
     */
    public $unique = true;

    /**
     * @var bool
     */
    public $visible = false;

    /**
     * @var bool
     */
    public $editable = false;

}