<?php

namespace Layer\Asset;

use Assetic\Asset\AssetInterface;
use Assetic\AssetWriter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssetResponse extends StreamedResponse {

	protected $mimeTypes = [
		'css' => 'text/css',
		'js' => 'text/javascript'
	];

	public function __construct(AssetInterface $asset, AssetWriter $writer, $cachePath, array $headers = []) {

		$file = $asset->getTargetPath();

		$cachePath = $cachePath . '/' . $file;

		$cached = false;

		$cacheTime = time();

		if (is_file($cachePath)) {
			$mTime = $asset->getLastModified();
			$cacheTime = filemtime($cachePath);
			if ($mTime > $cacheTime) {
				@unlink($cachePath);
				$cacheTime = $mTime;
			} else {
				$cached = true;
			}
		}

		if (!$cached) {
			$writer->writeAsset($asset);
		}

		$stream = function () use ($cachePath) {
			readfile($cachePath);
		};

		$headers['Content-Length'] = filesize($cachePath);

		if (preg_match('/.+\.([a-zA-Z0-9]+)/', $file, $matches)) {
			$ext = $matches[1];
			if (isset($this->mimeTypes[$ext])) {
				$headers['Content-Type'] = $this->mimeTypes[$ext];
			}
		}

		parent::__construct($stream, 200, $headers);

		$date = new \DateTime();
		$date->setTimestamp($cacheTime);
		$this->setLastModified($date);

	}

}