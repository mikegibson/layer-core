<?php

namespace Layer\Twig;

use Layer\Application;
use Layer\View\ViewInterface;

/**
 * Class TwigView
 *
 * @package Layer\View
 */
class TwigView implements ViewInterface {

    /**
     * @var \Layer\Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $_template;

    /**
     * @param \Twig_Environment $twig
     * @param string $template
     */
    public function __construct(Application $app, $template) {
        $this->app = $app;
        $this->_template = $template;
    }

    /**
     * @param array $data
     * @return string
     */
    public function render(array $data) {
        return $this->app['twig']->render($this->_template, $data);
    }

}