<?php

namespace Layer\Media\Image;

use Layer\Media\File\File;

class Image implements ImageInterface {

	/**
	 * @var File
	 */
	private $file;

	/**
	 * @param File $file
	 */
	public function __construct(File $file) {
		if(!$file->isImage()) {
			throw new \InvalidArgumentException('The file is not an image.');
		}
		$this->file = $file;
	}

	/**
	 * @return File
	 */
	public function getFile() {
		return $this->file;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->getFile()->getId();
	}

	/**
	 * @return int
	 */
	public function getWidth() {
		return $this->getFile()->getImageWidth();
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->getFile()->getImageHeight();
	}

	/**
	 * @return string
	 */
	public function getMimeType() {
		return $this->getFile()->getMimeType();
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->getFile()->getSize();
	}

	/**
	 * @return string
	 */
	public function getExtension() {
		return $this->getFile()->getExtension();
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->getFile()->getHash();
	}

	/**
	 * @return string
	 */
	public function getAbsolutePath() {
		return $this->getFile()->getAbsolutePath();
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return $this->getFile()->getUpdated();
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return $this->getFile()->__toString();
	}

}