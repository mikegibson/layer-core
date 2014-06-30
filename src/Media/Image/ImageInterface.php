<?php

namespace Sentient\Media\Image;

use Sentient\Media\File\FileInterface;

interface ImageInterface extends FileInterface {

	public function getWidth();

	public function getHeight();

}