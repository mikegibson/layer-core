<?php

namespace Layer\Data\Field;

/**
 * Class TitleField
 *
 * @package Layer\DataScaffold\DataType\Field
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

}