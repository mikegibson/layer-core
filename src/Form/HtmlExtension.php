<?php

namespace Layer\Form;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Form\AbstractExtension;

class HtmlExtension extends AbstractExtension {

	private $reader;

	public function __construct(Reader $reader) {
		$this->reader = $reader;
	}

	/**
	 * Registers the types.
	 *
	 * @return FormTypeInterface[] An array of FormTypeInterface instances
	 */
	protected function loadTypes() {
		return [
			new HtmlType()
		];
	}

	protected function loadTypeGuesser() {
		return new HtmlTypeGuesser($this->reader);
	}

}