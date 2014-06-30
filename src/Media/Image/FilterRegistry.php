<?php

namespace Sentient\Media\Image;

use Imagine\Image\ImageInterface;

class FilterRegistry {

	private $filters = [];

	/**
	 * @param FilterInterface $filter
	 * @throws \InvalidArgumentException
	 */
	public function addFilter(FilterInterface $filter) {
		$name = $filter->getUniqueKey();
		if($this->hasFilter($name)) {
			throw new \InvalidArgumentException(sprintf('Filter %s is already loaded.', $name));
		}
		$this->filters[$name] = $filter;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasFilter($name) {
		return isset($this->filters[$name]);
	}

	/**
	 * @param $name
	 * @return FilterInterface
	 * @throws \InvalidArgumentException
	 */
	public function getFilter($name) {
		if(!$this->hasFilter($name)) {
			throw new \InvalidArgumentException(sprintf('Filter %s is not loaded.', $name));
		}
		return $this->filters[$name];
	}

	/**
	 * @return array
	 */
	public function getFilterNames() {
		return array_keys($this->filters);
	}

	/**
	 * @param $name
	 * @param ImageInterface $image
	 * @return ImageInterface
	 */
	public function applyFilter($name, ImageInterface $image) {
		return $this->getFilter($name)->apply($image);
	}

}