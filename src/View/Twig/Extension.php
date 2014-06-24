<?php

namespace Layer\View\Twig;

/**
 * Class TwigExtension
 *
 * @package Layer\View
 */
abstract class Extension extends \Twig_Extension {

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * @var string
	 */
	protected $_name;

	protected $functions = [];

	/**
	 * Initializes the runtime environment.
	 * This is where you can load some file that contains filter functions for instance.
	 *
	 * @param \Twig_Environment $twig
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

	/**
	 * Returns a list of functions to add to the existing list.
	 *
	 * @return array An array of functions
	 * @todo Use inflector?
	 */
	function getFunctions() {

		$functions = [];
		foreach ($this->functions as $method) {
			$name = $this->getName() . '_' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $method));
			$functions[] = new \Twig_SimpleFunction($name, [$this->helper, $method], [
				'is_safe' => ['html']
			]);
			$functions[] = [$this->helper, $method];
		}

		return $functions;
	}

}