<?php

namespace Layer\View\Twig;

/**
 * Class TwigTableExtension
 *
 * @package Layer\View\Table
 */
abstract class TemplateBlockFunctionExtension extends \Twig_Extension {

	protected $template;

	protected $theme;

	protected $functionBlocks = [];

	protected $globalVars = [];

	protected $environment = null;

	private $__templateResource;

	public function initRuntime(\Twig_Environment $environment) {
		$this->environment = $environment;
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
				return $this->displayBlock($block, $args, $options);
			}, ['is_safe' => ['html']]);
		}
		return $functions;
	}

	public function displayBlock($block, array $args = [], array $options = []) {
		$vars = array_merge($this->globalVars, compact('args'));
		if(isset($options['vars'])) {
			$vars = array_merge($vars, $options['vars']);
		}
		if(isset($options['args'])) {
			$min = min([count($args), count($options['args'])]);
			for($i = 0; $i < $min; $i++) {
				if(isset($options['args'][$i]) && isset($args[$i])) {
					$vars[$options['args'][$i]] = $args[$i];
				}
			}
		}
		$context = $this->environment->mergeGlobals($vars);
		$context = $this->beforeRender($block, $context);
		ob_start();
		$this->_getTemplateResource()->displayBlock($block, $context);
		return ob_get_clean();
	}

	public function beforeRender($block, array $context) {
		return $context;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function getTheme() {
		return $this->theme;
	}

	public function setTheme($theme) {
		$this->__templateResource = null;
		$this->theme = $theme;
	}

	public function clearTheme() {
		$this->setTheme(null);
	}

	protected final function _getTemplateResource() {
		if($this->__templateResource === null) {
			$template = $this->theme ?: $this->template;
			$this->__templateResource = $this->environment->loadTemplate($template);
		}
		return $this->__templateResource;
	}

}