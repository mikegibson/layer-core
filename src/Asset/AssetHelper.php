<?php

namespace Layer\Asset;

use Assetic\AssetManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AssetHelper {

	/**
	 * @var \Assetic\AssetManager
	 */
	protected $assetManager;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	protected $urlGenerator;

	/**
	 * @param AssetManager $assetManager
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(AssetManager $assetManager, UrlGeneratorInterface $urlGenerator) {
		$this->assetManager = $assetManager;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @param $name
	 * @param bool $timestamp
	 * @return mixed
	 */
	public function url($name, $timestamp = true) {
		$asset = $this->assetManager->get($name);
		$params = ['asset' => $asset->getTargetPath()];
		if ($timestamp) {
			$params['v'] = $asset->getLastModified();
		}
		return $this->urlGenerator->generate('asset', $params);
	}

}