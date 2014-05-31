<?php

namespace Layer\Cms\View;

use Silex\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CmsHelper {

	private $urlGenerator;

	public function __construct(UrlGeneratorInterface $urlGenerator) {
		$this->urlGenerator = $urlGenerator;
	}

	public function url($nodePath, array $params = [])  {
		$params['node'] = $nodePath;
		return $this->urlGenerator->generate('cms', $params);
	}

}