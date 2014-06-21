<?php

namespace Layer\Media\Image;

interface FilterInterface extends \Imagine\Filter\FilterInterface {

	public function getUniqueKey();

}