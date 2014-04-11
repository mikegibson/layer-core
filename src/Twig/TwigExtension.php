<?php

namespace Layer\Twig;

/**
 * Class TwigExtension
 *
 * @package Layer\View
 */
abstract class TwigExtension extends \Twig_Extension {

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $_name;

    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param Twig_Environment $environment The current Twig_Environment instance
     */
    public function initRuntime(\Twig_Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName() {
        if ($this->_name === null) {
            $name = get_class($this);
            $pos = strrpos($name, '\\');
            if ($pos !== false) {
                $name = substr($name, $pos + 1);
            }
            if (preg_match('/^Twig([A-Za-z]+)Extension$/', $name, $matches)) {
                $name = $matches[1];
            }
            $this->_name = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        }
        return $this->_name;
    }

}