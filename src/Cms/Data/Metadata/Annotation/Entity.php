<?php

namespace Layer\Cms\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CmsEntity
 * @package Layer\Cms\Data\Metadata\Annotation
 * @Annotation
 */
class Entity extends Annotation {

	public $slug;

}