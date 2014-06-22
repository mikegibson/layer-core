<?php

namespace Layer\Media\Image;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Layer\Data\Metadata\Annotation as Layer;
use Layer\Media\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="images")
 * @Layer\CrudEntity(create=false, update=false)
 * @Layer\EntityName("images")
 */
class Image implements ImageInterface {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @Layer\PropertyLabel("ID")
	 */
	protected $id;

	/**
	 * @var File $file
	 *
	 * @ORM\OneToOne(targetEntity="Layer\Media\File\File", mappedBy="image")
	 * @ORM\JoinColumn(name="file_id", referencedColumnName="id", nullable=false)
	 */
	protected $file;

	/**
	 * @var int $width
	 *
	 * @ORM\Column(type="integer")
	 * @Layer\CrudProperty(editable=false)
	 */
	protected $width;

	/**
	 * @var int $height
	 *
	 * @ORM\Column(type="integer")
	 * @Layer\CrudProperty(editable=false)
	 */
	protected $height;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
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
	public function getWidth() {
		return $this->width;
	}

	/**
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * @return string
	 */
	public function getMimeType() {
		return ($file = $this->getFile()) ? $file->getMimeType() : null;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return ($file = $this->getFile()) ? $file->getSize() : null;
	}

	/**
	 * @return string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getExtension() {
		return ($file = $this->getFile()) ? $file->getExtension() : null;
	}

	/**
	 * @return string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getHash() {
		return ($file = $this->getFile()) ? $file->getHash() : null;
	}

	/**
	 * @return string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getAbsolutePath() {
		return ($file = $this->getFile()) ? $file->getAbsolutePath() : null;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return ($file = $this->getFile()) ? $file->getUpdated() : null;
	}

	/**
	 * @param File $file
	 */
	public function __setFile(File $file) {
		$this->ensureCreate();
		$this->file = $file;
	}

	public function __setWidth($width) {
		$this->ensureCreate();
		$this->width = $width;
	}

	public function __setHeight($height) {
		$this->ensureCreate();
		$this->height = $height;
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return ($file = $this->getFile()) ? $file->__toString() : '';
	}

	/**
	 * @throws \BadMethodCallException
	 */
	protected function ensureCreate() {
		if($this->getId()) {
			throw new \BadMethodCallException('This method should only be called on create.');
		}
	}

}