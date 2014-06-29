<?php

namespace Layer\Media\File;

class FilesystemFile implements FileInterface {

	protected $mimetype;

	private $absolutePath;

	public function __construct($path, $mimetype = null) {
		if(!is_file($path)) {
			throw new \InvalidArgumentException(sprintf('The path %s is not valid.', $path));
		}
		$this->absolutePath = $path;
		if($mimetype !== null) {
			$this->mimetype = $mimetype;
		}
	}

	public function getSize() {
		return filesize($this->getAbsolutePath());
	}

	public function getExtension() {
		if(preg_match('#^.+\.([a-z0-9]{1,10})$#i', $this->getAbsolutePath(), $matches)) {
			return $matches[1];
		}
	}

	public function getMimeType() {
		if($this->mimetype !== null) {
			return $this->mimetype;
		}
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		return finfo_file($finfo, $this->getAbsolutePath());
	}

	public function getHash() {
		return md5_file($this->getAbsolutePath());
	}

	public function getUpdated() {
		$time = new \DateTime();
		$time->setTimestamp(filemtime($this->getAbsolutePath()));
		return $time;
	}

	public function getAbsolutePath() {
		return $this->absolutePath;
	}

}