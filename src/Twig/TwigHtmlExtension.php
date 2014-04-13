<?php

namespace Layer\Twig;

use Layer\View\Html\HtmlHelper;

/**
 * Class TwigHtmlExtension
 *
 * @package Layer\Twig
 */
class TwigHtmlExtension extends TwigExtension {

	/**
	 * @var \Layer\View\Html\HtmlHelper
	 */
	protected $helper;

	/**
	 * @param HtmlHelper $helper
	 */
	public function __construct(HtmlHelper $helper) {

		$this->helper = $helper;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {

		$functions = [];
		$functions[] = new \Twig_SimpleFunction('link', [$this->helper, 'link'], [
			'is_safe' => ['html']
		]);

		return $functions;
	}

}