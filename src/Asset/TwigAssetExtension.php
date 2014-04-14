<?php

namespace Layer\Asset;

use Layer\View\Twig\Extension;

class TwigAssetExtension extends Extension {

	protected $helper;

	protected $functions = ['url'];

	public function __construct(AssetHelper $helper) {
		$this->helper = $helper;
	}

}