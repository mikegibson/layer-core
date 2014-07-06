<?php

namespace Sentient\Media\Image;

use Sentient\Asset\FileInterface;

interface ImageInterface extends FileInterface {

	public function getWidth();

	public function getHeight();

}