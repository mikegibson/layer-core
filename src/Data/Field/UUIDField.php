<?php

namespace Layer\Data\Field;

/**
 * Class UUIDField
 * @package Layer\Data\Field
 */
class UUIDField extends PrimaryKeyField {

	/**
	 * @var string
	 */
	public $type = 'uuid';

	/**
	 * @var string
	 */
	public $name = 'uuid';

}