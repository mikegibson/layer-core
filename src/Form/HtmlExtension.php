<?php

namespace Layer\Form;

use Symfony\Component\Form\AbstractExtension;

class HtmlExtension extends AbstractExtension {

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

}