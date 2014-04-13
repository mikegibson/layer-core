<?php

namespace Layer\Admin\View;

use Layer\Twig\TwigExtension;

/**
 * Class TwigAdminExtension
 *
 * @package Layer\Admin\View
 */
class TwigAdminExtension extends TwigExtension {

	/**
	 * @var AdminHelper
	 */
	protected $helper;

	/**
	 * @var array
	 */
	protected $methods = ['link', 'url'];

	/**
	 * @param AdminHelper $helper
	 */
	public function __construct(AdminHelper $helper) {

		$this->helper = $helper;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {

		$functions = [];
		foreach ($this->methods as $method) {
			$functions[] = new \Twig_SimpleFunction(
				'admin_' . $method,
				[$this->helper, $method],
				[
					'is_safe' => ($method === 'url') ? false : ['html']
				]
			);

		}

		return $functions;
	}

}