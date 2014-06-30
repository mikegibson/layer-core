<?php

namespace Sentient\View\Twig;

/**
 * Class TemplateBlockThemeNode
 * @package Sentient\View\Twig
 */
class TemplateBlockThemeNode extends \Twig_Node {

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @param $name
	 */
	public function setExtensionName($name) {
		$this->extensionName = $name;
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param \Twig_Compiler $compiler A Twig_Compiler instance
	 */
	public function compile(\Twig_Compiler $compiler) {
		$compiler
			->addDebugInfo($this)
			->write("\$this->env->getExtension('{$this->extensionName}')->getRenderer()->setTheme(")
			->subcompile($this->getNode('theme'))
			->raw(");\n");
		;
	}

}
