<?php

namespace Layer\Data\Field;

/**
 * Class TitleField
 * @package Layer\Data\Field
 */
class TitleField extends StringField {

	/**
	 * @var bool
	 */
	public $index = true;

	/**
	 * @var bool
	 */
	public $null = false;

	/**
	 * @var bool
	 */
	public $titleField = true;

}