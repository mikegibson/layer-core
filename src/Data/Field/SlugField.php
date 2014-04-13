<?php

namespace Layer\Data\Field;

/**
 * Class SlugField
 *
 * @package Layer\DataScaffold\DataType\Field
 */
class SlugField extends StringField {

	/**
	 * @var string
	 */
	public $type = 'string';

	/**
	 * @var int
	 */
	public $length = 200;

	/**
	 * @var bool
	 */
	public $fixed = false;

	/**
	 * @var bool
	 */
	public $null = false;

	/**
	 * @var bool
	 */
	public $allowEmpty = false;

	/**
	 * @var string
	 */
	public $pattern = '/^[a-z0-9]([a-z0-9\-]*[a-z0-9])?$$/';

	/**
	 * @var bool
	 */
	public $important = false;

	public $htmlSafe = true;

}