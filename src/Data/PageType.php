<?php

namespace Layer\Data;

/**
 * Class PageType
 *
 * @package Layer\Pages\DataType
 */
class PageType extends DataType {

	/**
	 * @var string
	 */
	public $namespace = 'content';

	public $entityClass = 'Layer\Entity\Content\Page';

	public $titleField = 'title';

}