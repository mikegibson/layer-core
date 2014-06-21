<?php

namespace Layer\Media\Image;

use Imagine\Image\ImagineInterface;

class FilteredImageWriter {

	protected $imagine;

	protected $cacheRoot;

	public function __construct(ImagineInterface $imagine, $cacheRoot) {
		$this->imagine = $imagine;
		$this->cacheRoot = $cacheRoot;
	}

	public function getFilteredImagePath(ImageInterface $image, FilterInterface $filter) {
		$cachePath = $this->cacheRoot . '/' . $filter->getUniqueKey() . '/' . $image->getHash() . '.' . $image->getExtension();
		if(!is_file($cachePath)) {
			$dir = dirname($cachePath);
			if(!is_dir($dir)) {
				mkdir($dir, 0777, true);
			}
			$filter->apply($this->imagine->open($image->getAbsolutePath()))->save($cachePath);
		}
		return $cachePath;
	}

}