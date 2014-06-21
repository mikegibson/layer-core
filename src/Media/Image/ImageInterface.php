<?php

namespace Layer\Media\Image;

use Layer\Media\File\FileInterface;

interface ImageInterface extends FileInterface {

	public function getWidth();

	public function getHeight();

}