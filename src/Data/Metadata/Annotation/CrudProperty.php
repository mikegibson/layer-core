<?php

namespace Layer\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CrudProperty
 * @package Layer\Data\Metadata\Annotation
 * @Annotation
 */
class CrudProperty extends Annotation {

	const EDITABLE_ALWAYS = true;

	const EDITABLE_NEVER = false;

	const EDITABLE_ON_CREATE = 'CREATE';

	const EDITABLE_ON_UPDATE = 'UPDATE';

	public $visible;

	public $editable = self::EDITABLE_ALWAYS;

	public static $mergeDefault = true;

}