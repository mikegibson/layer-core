<?php

namespace Sentient\Form;

class HtmlPurifier implements HtmlPurifierInterface {

	private $purifier;

	public function __construct(\HTMLPurifier $purifier) {
		$this->purifier = $purifier;
	}

	public function purify($html) {
		return $this->purifier->purify($html);
	}

}