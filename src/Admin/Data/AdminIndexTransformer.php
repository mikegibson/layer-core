<?php

namespace Layer\Admin\Data;

use Layer\Data\Transformer;

/**
 * Class AdminIndexTransformer
 * @package Layer\Admin\Data
 */
class AdminIndexTransformer extends Transformer {

	/**
	 * @var int
	 */
	public $truncate = 100;

	/**
	 * @var bool
	 */
	public $stripTags = true;

}