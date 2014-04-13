<?php

namespace Layer\Asset;

use Layer\Twig\TwigExtension;

class TwigAssetExtension extends TwigExtension {

	protected $helper;

	protected $functions = ['url'];

	public function __construct(AssetHelper $helper) {
		$this->helper = $helper;
	}

}