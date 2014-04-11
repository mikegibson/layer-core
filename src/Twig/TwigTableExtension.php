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
    protected $methods = [
        'render', 'wrap', 'thead', 'headerRow', 'headerColumns', 'tbody', 'bodyRows', 'row', 'headerCell', 'cell'
    ];

    /**
     * @param TableHelperInterface $view
     */
    public function __construct(TableHelperInterface $view) {
        $this->helper = $view;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     * @todo Use inflector?
     */
    function getFunctions() {

        $functions = [];
        foreach ($this->methods as $method) {
            $name = $this->getName() . '_' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $method));
            $functions[] = new \Twig_SimpleFunction($name, [$this->helper, $method], [
                'is_safe' => ['html']
            ]);
            $functions[] = [$this->helper, $method];
        }

        return $functions;
    }

}