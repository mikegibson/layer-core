<?php

namespace Sentient\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CrudProperty
 * @package Sentient\Data\Metadata\Annotation
 * @Annotation
 */
class CrudProperty extends Annotation {

	const EDITABLE_ALWAYS = true;

	const EDITABLE_NEVER = false;

	const EDITABLE_ON_CREATE = 'create';

	const EDITABLE_ON_UPDATE = 'update';

	public $visible = true;

	public $editable = self::EDITABLE_ALWAYS;

	public static $mergeDefault = true;

}