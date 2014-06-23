<?php

namespace Layer\Media\File;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Layer\Cms\Data\Metadata\Annotation as Cms;
use Layer\Data\Metadata\Annotation as Layer;
use Layer\Media\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="files")
 * @Layer\CrudEntity
 * @Layer\EntityName("files")
 * @Cms\RootNodePath("media/files")
 */
class File implements FileInterface {

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
	 * @var string $filename
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Layer\TitleProperty
	 */
	protected $filename;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $title;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $description;

	/**
	 * @var string $mimeType
	 *
	 * @ORM\Column(type="string")
	 */
	protected $mimeType;

	/**
	 * @var int $size
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $size;

	/**
	 * @var string $path
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Layer\CrudProperty(visible=false, editable=false)
	 */
	protected $path;

	/**
	 * @var string $hash
	 *
	 * @ORM\Column(type="string")
	 * @Layer\CrudProperty(visible=false, editable=false)
	 * @todo Make unique, no point storing duplicate files
	 */
	protected $hash;

	/**
	 * @var bool $webAccessible
	 *
	 * @ORM\Column(type="boolean")
	 */
	protected $webAccessible;

	/**
	 * @var \DateTime $created
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="create")
	 * @Layer\InvisibleProperty
	 */
	protected $created;

	/**
	 * @var \DateTime $updated
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="update")
	 * @Layer\InvisibleProperty
	 */
	protected $updated;

	/**
	 * @var UploadedFile $file
	 * @Assert\File(maxSize="6000000")
	 * @Layer\CrudProperty(editable="create")
	 * @Layer\PropertyLabel("File")
	 */
	protected $uploadedFile;

	/**
	 * @var string
	 */
	protected $rootDir;

	/**
	 * @var string
	 */
	protected $rootWebPath;

	/**
	 * @var Image $image
	 *
	 * @ORM\OneToOne(targetEntity="Layer\Media\Image\Image", mappedBy="file")
	 * @Layer\InvisibleProperty
	 */
	protected $image;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getMimeType() {
		return $this->mimeType;
	}

	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}

	/**
	 * @return bool
	 */
	public function isImage() {
		return $this->image instanceof Image;
	}

	/**
	 * @return Image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * @return string
	 */
	public function getHash() {
		return $this->hash;
	}

	public function isWebAccessible() {
		return $this->webAccessible;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @return string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getAbsolutePath() {
		return $this->rootDir . '/' . $this->getPath();
	}

	/**
	 * @return string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getWebPath() {
		return $this->rootDir . '/' . $this->getFilename();
	}

	/**
	 * @return null|string
	 *
	 * @Layer\InvisibleProperty
	 */
	public function getExtension() {
		$filename = $this->getFilename();
		$pos = strrpos($filename, '.');
		if($pos === false || $pos === 0 || $pos >= strlen($filename) - 1) {
			return null;
		}
		return substr($filename, $pos + 1);
	}

	/**
	 * @return null|UploadedFile
	 */
	public function getUploadedFile() {
		return $this->uploadedFile;
	}

	/**
	 * @param UploadedFile $file
	 */
	public function setUploadedFile(UploadedFile $file) {
		$this->ensureCreate();
		$this->uploadedFile = $file;
	}

	/**
	 * @param string $name
	 * @todo Escape this somehow
	 */
	public function setFilename($name) {
		$this->filename = $name;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * @param string $mimeType
	 */
	public function setMimeType($mimeType) {
		$this->mimeType = $mimeType;
	}

	/**
	 * @param bool $webAccessible
	 */
	public function setWebAccessible($webAccessible) {
		$this->webAccessible = $webAccessible;
	}

	public function __setPath($path) {
		$this->ensureCreate();
		$this->path = $path;
	}

	public function __setSize($size) {
		$this->ensureCreate();
		$this->size = $size;
	}

	public function __setImage(Image $image) {
		$this->image = $image;
	}

	public function __setHash($hash) {
		$this->ensureCreate();
		$this->hash = $hash;
	}

	public function __setRootDir($rootDir) {
		$this->rootDir = $rootDir;
	}

	public function __setRootWebPath($rootWebPath) {
		$this->rootWebPath = $rootWebPath;
	}

	public function __toString() {
		return $this->getTitle() ?: $this->getFilename();
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