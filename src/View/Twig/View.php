<?php

namespace Sentient\View\Twig;

use Sentient\View\ViewInterface;

/**
 * Class TwigView
 *
 * @package Sentient\View
 */
class View implements ViewInterface {

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @param \Twig_Environment $twig
	 */
	public function __construct(\Twig_Environment $twig) {
		$this->twig = $twig;
	}

	/**
	 * @param array $template
	 * @param array $data
	 * @return string
	 */
	public function render($template, array $data = []) {
		return $this->getTwig()->render($template, $data);
	}

	/**
	 * @return \Twig_Environment
	 */
	protected function getTwig() {
		return $this->twig;
	}

}