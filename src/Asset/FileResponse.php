<?php

namespace Sentient\Asset;

use Sentient\Media\File\FileInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileResponse extends StreamedResponse {

	/**
	 * @param FileInterface $file
	 * @param array $headers
	 */
	public function __construct(FileInterface $file, array $headers = []) {

		$stream = function () use ($file) {
			readfile($file->getAbsolutePath());
		};

		$headers['Content-Length'] = $file->getSize();
		$headers['Content-Type'] = $file->getMimeType();

		parent::__construct($stream, 200, $headers);

		$mtime = $file->getUpdated();

		$this->setCache([
			'etag' => (string) $mtime->getTimestamp(),
			'last_modified' => $mtime,
			'public' => true
		]);

	}

}