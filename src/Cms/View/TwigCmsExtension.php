<?php

namespace Sentient\Cms\View;

use Sentient\View\Twig\Extension;

/**
 * Class TwigCmsExtension
 *
 * @package Sentient\Cms\View
 */
class TwigCmsExtension extends Extension {

	/**
	 * @var CmsHelper
	 */
	protected $helper;

	/**
	 * @var array
	 */
	protected $methods = ['url', 'repository_nav'];

	/**
	 * @param CmsHelper $helper
	 */
	public function __construct(CmsHelper $helper) {

		$this->helper = $helper;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {

		$functions = [];
		foreach ($this->methods as $method) {
			$functions[] = new \Twig_SimpleFunction(
				'cms_' . $method,
				[$this->helper, $method]
			);

		}

		return $functions;
	}

}