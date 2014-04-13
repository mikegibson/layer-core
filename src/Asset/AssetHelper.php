<?php

namespace Layer\Asset;

use Layer\Application;
use Layer\Route\UrlGeneratorTrait;

class AssetHelper {

	use UrlGeneratorTrait;

	/**
	 * @var \Layer\Application
	 */
	protected $app;

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * @param $name
	 * @param bool $timestamp
	 * @return mixed
	 */
	public function url($name, $timestamp = true) {
		$asset = $this->app['assetic.asset_manager']->get($name);
		$params = ['asset' => $asset->getTargetPath()];
		if ($timestamp) {
			$params['v'] = $asset->getLastModified();
		}
		return $this->generateUrl($this->app, 'asset', $params);
	}

}