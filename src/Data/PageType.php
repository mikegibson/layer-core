<?php

namespace Layer\Data;

/**
 * Class PageType
 *
 * @package Layer\Pages\DataType
 */
class PageType extends DataType {

    /**
     * @var string
     */
    public $plugin = 'Pages';

    /**
     * @var string
     */
    public $namespace = 'content';

    /**
     * @var array
     */
    protected $_fields = [
        'id',
        'title',
        'slug',
        'content',
    ];
}