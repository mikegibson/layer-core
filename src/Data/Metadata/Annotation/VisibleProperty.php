<?php

namespace Sentient\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class VisibleProperty
 * @package Sentient\Data\Metadata\Annotation
 * @Annotation
 */
class VisibleProperty extends Annotation {

	public $important = true;

}