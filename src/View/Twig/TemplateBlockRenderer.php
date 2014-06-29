<?php

namespace Layer\View\Twig;

/**
 * Class TemplateBlockRenderer
 * @package Layer\View\Twig
 */
class TemplateBlockRenderer {

	/**
	 * @var TemplateBlockFunctionExtension
	 */
	protected $extension;

	/**
	 * @var \Twig_Environment
	 */
	protected $environment;

	protected $theme;

	private $__templateResources = [];

	/**
	 * @param TemplateBlockFunctionExtension $extension
	 * @param \Twig_Environment $environment
	 */
	public function __construct(TemplateBlockFunctionExtension $extension, \Twig_Environment $environment) {
		$this->extension = $extension;
		$this->environment = $environment;
	}

	public function getTheme() {
		return $this->theme;
	}

	/**
	 * @param $block
	 * @param array $args
	 * @param array $options
	 * @return string
	 */
	public function displayBlock($block, array $args = [], array $options = []) {
		$vars = array_merge($this->extension->getGlobalVars(), compact('args'));
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
		$theme = $vars['theme'] = isset($vars['theme']) ? $vars['theme'] : $this->theme ?: $this->extension->getTemplate();
		$context = $this->environment->mergeGlobals($vars);
		$context = $this->extension->beforeRender($block, $context);
		ob_start();
		$this->_getTemplateResource($theme)->displayBlock($block, $context);
		return ob_get_clean();
	}

	/**
	 * @param $theme
	 */
	public function setTheme($theme) {
		$this->__templateResource = null;
		$this->theme = $theme;
	}

	public function clearTheme() {
		$this->setTheme(null);
	}

	protected final function _getTemplateResource($template) {
		if(!isset($this->__templateResources[$template])) {
			$this->__templateResources[$template] = $this->environment->loadTemplate($template);
		}
		return $this->__templateResources[$template];
	}

}