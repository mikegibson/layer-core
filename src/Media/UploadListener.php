<?php

namespace Layer\Media;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Layer\Media\File\File;
use Layer\Media\Image\Image;

class UploadListener implements EventSubscriber {

	/**
	 * @var \Doctrine\Common\Persistence\ManagerRegistry
	 */
	private $repository;

	/**
	 * @var string
	 */
	protected $rootDir;

	/**
	 * @var string
	 */
	protected $rootWebPath;

	/**
	 * @var array
	 */
	protected $imageMimeTypes = [
		'image/gif',
		'image/jpeg',
		'image/png'
	];

	/**
	 * @param ObjectRepository $repository
	 * @param $rootDir
	 * @param $rootWebPath
	 */
	public function __construct(ObjectRepository $repository, $rootDir, $rootWebPath) {
		$this->repository = $repository;
		$this->rootDir = $rootDir;
		$this->rootWebPath = $rootWebPath;
	}

	/**
	 * @return array
	 */
	public function getSubscribedEvents() {
		return [
			'postLoad',
			'prePersist',
			'postPersist',
			'postRemove'
		];
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postLoad(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		$file->__setRootDir($this->rootDir);
		$file->__setRootWebPath($this->rootWebPath);
		if($file->isImage()) {
			$file->__setImage(new Image($file));
		}
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function prePersist(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		if(!$file->getFilename()) {
			$file->setFilename($this->generateFilename($file));
		}
		$uploadedFile = $file->getUploadedFile();
		$mimeType = $uploadedFile->getMimeType();
		if(!$file->getMimeType()) {
			$file->setMimeType($mimeType);
		}
		$isImage = in_array($mimeType, $this->imageMimeTypes);
		$file->__setIsImage($isImage);
		if($isImage) {
			$size = getimagesize($uploadedFile->getRealPath());
			$file->__setImageWidth($size[0]);
			$file->__setImageHeight($size[1]);
		}
		$file->__setSize($uploadedFile->getClientSize());
		$file->__setPath($this->generatePath($file));
		$file->__setHash($this->generateHash($file));
	}

	/**
	 * @param PreUpdateEventArgs $event
	 */
	public function preUpdate(PreUpdateEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postPersist(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		$file->getUploadedFile()->move($this->rootDir, $file->getPath());
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postRemove(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		@unlink($file->getAbsolutePath());
	}

	/**
	 * @param LifecycleEventArgs $event
	 * @return null|File
	 */
	protected function getFile(LifecycleEventArgs $event) {
		$file = $event->getEntity();
		if($file instanceof File) {
			return $file;
		}
	}

	/**
	 * @param File $file
	 * @return string
	 */
	protected function generateFilename(File $file) {
		$file = $file->getUploadedFile();
		$filename = $file->getClientOriginalName();
		if($this->fileExists($filename)) {
			$ext = $file->getClientOriginalExtension();
			if($ext) {
				$basename = substr($filename, 0, - strlen($ext) - 1);
				$ext = '.' . $ext;
			} else {
				$basename = $filename;
			}
			$i = 1;
			while($this->fileExists($filename = $basename . '-' . $i . $ext)) {
				$i++;
			}
		}
		return $filename;
	}

	/**
	 * @param File $file
	 * @return string
	 * @todo Use generateHash() to generate this once the hash has been made unique
	 */
	protected function generatePath(File $file) {
		$file = $file->getUploadedFile();
		return sha1(uniqid(mt_rand(), true)) . '.' . $file->guessExtension();
	}

	/**
	 * @param File $file
	 * @return string
	 */
	protected function generateHash(File $file) {
		return md5_file($file->getUploadedFile()->getRealPath());
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	protected function fileExists($filename) {
		return !!$this->getRepository()->findOneBy(compact('filename'));
	}

	/**
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	protected function getRepository() {
		return $this->repository;
	}

}