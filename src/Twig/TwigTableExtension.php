<?php

namespace Layer\Twig;

use Layer\View\Table\TableHelperInterface;

/**
 * Class TwigTableHelper
 *
 * @package Layer\View\Table
 */
class TwigTableExtension extends TwigExtension {

    /**
     * @var TableHelperInterface
     */
    protected $helper;

    /**
     * @var array
     */
    protected $functions = [
        'render', 'wrap', 'thead', 'headerRow', 'headerColumns', 'tbody', 'bodyRows', 'row', 'headerCell', 'cell'
    ];

    /**
     * @param TableHelperInterface $view
     */
    public function __construct(TableHelperInterface $view) {
        $this->helper = $view;
    }

}