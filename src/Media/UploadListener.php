<?php

namespace Sentient\Media;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Media\File\File;

class UploadListener implements EventSubscriber {

	/**
	 * @var \Sentient\Data\ManagedRepositoryInterface
	 */
	private $fileRepository;

	/**
	 * @var \Sentient\Data\ManagedRepositoryInterface
	 */
	private $imageRepository;

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
	 * @param ManagedRepositoryInterface $fileRepository
	 * @param ManagedRepositoryInterface $imageRepository
	 * @param $rootDir
	 * @param $rootWebPath
	 */
	public function __construct(
		ManagedRepositoryInterface $fileRepository,
		ManagedRepositoryInterface $imageRepository,
		$rootDir,
		$rootWebPath
	) {
		$this->fileRepository = $fileRepository;
		$this->imageRepository = $imageRepository;
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
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function prePersist(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		if(!$uploadedFile = $file->getUploadedFile()) {
			return;
		}
		if(!$file->getFilename()) {
			$file->setFilename($this->generateFilename($file));
		}
		$mimeType = $uploadedFile->getClientMimeType();
		if(!$file->getMimeType()) {
			$file->setMimeType($mimeType);
		}
		$file->__setSize($uploadedFile->getClientSize());
		$file->__setPath($this->generatePath($file));
		$file->__setHash($this->generateHash($file));
	}

	/**
	 * @param LifecycleEventArgs $event
	 */
	public function postPersist(LifecycleEventArgs $event) {
		if(!$file = $this->getFile($event)) {
			return;
		}
		$file->getUploadedFile()->move($this->rootDir, $file->getPath());
		if(in_array($file->getMimeType(), $this->imageMimeTypes)) {
			$image = $this->imageRepository->createEntity();
			$image->__setFile($file);
			$size = getimagesize($this->rootDir . '/' . $file->getPath());
			$image->__setWidth($size[0]);
			$image->__setHeight($size[1]);
			$this->getImageRepository()->save($image);
		}
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
		if($this->filenameExists($filename)) {
			$ext = $file->getClientOriginalExtension();
			if($ext) {
				$basename = substr($filename, 0, - strlen($ext) - 1);
				$ext = '.' . $ext;
			} else {
				$basename = $filename;
			}
			$i = 1;
			while($this->filenameExists($filename = $basename . '-' . $i . $ext)) {
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
	protected function filenameExists($filename) {
		return !!$this->getFileRepository()->findOneBy(compact('filename'));
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	protected function getFileRepository() {
		return $this->fileRepository;
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	protected function getImageRepository() {
		return $this->imageRepository;
	}

}