<?php

namespace Layer\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class VisibleProperty
 * @package Layer\Data\Metadata\Annotation
 * @Annotation
 */
class VisibleProperty extends Annotation {

	public $important = true;

}