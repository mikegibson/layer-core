<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractExtension;

class HtmlExtension extends AbstractExtension {

	private $htmlType;

	private $htmlTypeGuesser;

	public function __construct(HtmlType $htmlType, HtmlTypeGuesser $htmlTypeGuesser) {
		$this->htmlType = $htmlType;
		$this->htmlTypeGuesser = $htmlTypeGuesser;
	}

	/**
	 * Registers the types.
	 *
	 * @return FormTypeInterface[] An array of FormTypeInterface instances
	 */
	protected function loadTypes() {
		return [
			$this->htmlType
		];
	}

	protected function loadTypeGuesser() {
		return $this->htmlTypeGuesser;
	}

}