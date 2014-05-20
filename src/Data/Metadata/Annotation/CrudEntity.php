<?php

namespace Layer\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class CrudEntity
 * @package Layer\Data\Metadata\Annotation
 * @Annotation
 */
class CrudEntity extends Annotation {

	public $create = true;

	public $read = true;

	public $update = true;

	public $delete = true;

}