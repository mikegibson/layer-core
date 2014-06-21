<?php

namespace Layer\Media\File;

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

		$this->setLastModified($file->getUpdated());

	}

}