<?php

namespace Sentient\Form;

interface HtmlPurifierInterface {

	/**
	 * @param string $html
	 * @return string
	 */
	public function purify($html);

}