<?php

namespace Sentient\Media\Image;

interface FilterInterface extends \Imagine\Filter\FilterInterface {

	public function getUniqueKey();

}