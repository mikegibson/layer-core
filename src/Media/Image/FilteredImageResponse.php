<?php

namespace Sentient\Media\Image;

use Symfony\Component\HttpFoundation\StreamedResponse;

class FilteredImageResponse extends StreamedResponse {

	/**
	 * @param ImageInterface $image
	 * @param FilterInterface $filter
	 * @param FilteredImageWriter $writer
	 * @param array $headers
	 */
	public function __construct(
		ImageInterface $image,
		FilterInterface $filter,
		FilteredImageWriter $writer,
		array $headers = []
	) {

		$path = $writer->getFilteredImagePath($image, $filter);

		$stream = function () use ($path) {
			readfile($path);
		};

		$headers['Content-Length'] = filesize($path);
		$headers['Content-Type'] = $image->getMimeType();

		parent::__construct($stream, 200, $headers);

		$this->setLastModified($image->getUpdated());

	}

}