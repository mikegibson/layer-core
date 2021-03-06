<?php

namespace Sentient\Cms\View;

use Sentient\Cms\Node\CmsRepositoryNavigationNode;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Node\ControllerNodeInterface;
use Silex\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CmsHelper {

	private $urlGenerator;

	public function __construct(UrlGeneratorInterface $urlGenerator) {
		$this->urlGenerator = $urlGenerator;
	}

	public function url($nodePath = '', array $params = [])  {
		$params['node'] = $nodePath;
		return $this->urlGenerator->generate('cms', $params);
	}

	public function repository_nav(ManagedRepositoryInterface $repository, ControllerNodeInterface $currentNode = null) {
		return new CmsRepositoryNavigationNode($repository, $this->urlGenerator, $currentNode);
	}

}