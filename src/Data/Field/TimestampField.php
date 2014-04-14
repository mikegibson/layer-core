<?php

namespace Layer\Data\Field;

/**
 * Class TimestampField
 * @package Layer\Data\Field
 */
class TimestampField extends DatetimeField {

	/**
	 * @var string
	 */
	public $type = 'timestamp';

	public $htmlSafe = true;

}