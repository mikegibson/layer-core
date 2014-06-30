<?php

namespace Sentient\Media\Image;

use Imagine\Filter\Transformation;
use Imagine\Image\ImageInterface;

class ImageTransformer implements FilterInterface {

	private $uniqueKey;

	private $transformation;

	public function __construct($uniqueKey) {
		$this->uniqueKey = $uniqueKey;
		$this->transformation = new Transformation();
	}

	public function getUniqueKey() {
		return $this->uniqueKey;
	}

	public function apply(ImageInterface $image) {
		return $this->getTransformation()->apply($image);
	}

	public function getTransformation() {
		return $this->transformation;
	}

}