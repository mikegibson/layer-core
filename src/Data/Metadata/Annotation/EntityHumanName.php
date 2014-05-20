<?php

namespace Layer\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class EntityHumanName
 * @package Layer\Data\Metadata\Annotation
 * @Annotation
 */
class EntityHumanName extends Annotation {

	public $singular;

	public $plural;

}