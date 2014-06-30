<?php

namespace Sentient\Cms\Data\Metadata\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class FormProperty
 * @package Sentient\Cms\Data\Metadata\Annotation
 * @Annotation
 */
class FormFieldProperty extends Annotation {

	public $options = [];

	public function __get($name) {
		if($name === 'type') {
			return $this->value;
		}
		return parent::__get($name);
	}

	public function __set($name, $value) {
		if($name === 'type') {
			$this->value = $value;
			return;
		}
		return parent::__set($name, $value);
	}

}