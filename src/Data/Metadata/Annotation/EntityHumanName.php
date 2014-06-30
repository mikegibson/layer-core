<?php

namespace Sentient\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class EntityHumanName
 * @package Sentient\Data\Metadata\Annotation
 * @Annotation
 */
class EntityHumanName extends Annotation {

	public $singular;

	public $plural;

}