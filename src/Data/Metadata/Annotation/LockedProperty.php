<?php

namespace Layer\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class LockedProperty
 * @package Layer\Data\Metadata\Annotation
 * @Annotation
 */
class LockedProperty extends Annotation {

	public $onCreate = true;

	public $onUpdate = true;

}