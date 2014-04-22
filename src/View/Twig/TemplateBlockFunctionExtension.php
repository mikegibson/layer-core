<?php

namespace Layer\View\Twig;

/**
 * Class TemplateBlockFunctionExtension
 * @package Layer\View\Twig
 */
abstract class TemplateBlockFunctionExtension extends \Twig_Extension {

	protected $template;

	protected $functionBlocks = [];

	protected $globalVars = [];

	protected $renderer;

	public function initRuntime(\Twig_Environment $environment) {
		$this->renderer = new TemplateBlockRenderer($this, $environment);
	}

	public function getTokenParsers() {
		return [
			new TemplateBlockThemeTokenParser($this)
		];
	}

	public function getFunctions() {
		$functions = [];
		foreach($this->functionBlocks as $block => $options) {
			if(is_int($block)) {
				$block = $options;
				$options = [];
			}
			$functions[$block] = new \Twig_Function_Function(function() use($block, $options) {
				$args = func_get_args();
				return $this->renderer->displayBlock($block, $args, $options);
			}, ['is_safe' => ['html']]);
		}
		return $functions;
	}

	public function getRenderer() {
		return $this->renderer;
	}

	public function getGlobalVars() {
		return $this->globalVars;
	}

	public function beforeRender($block, array $context) {
		return $context;
	}

	public function getTemplate() {
		return $this->template;
	}

}