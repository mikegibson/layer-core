<?php

namespace Sentient\Cms\Data;

use Sentient\Cms\Node\RepositoryCmsNodeFactory;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\WrappedManagedRepository;
use Sentient\Node\ControllerNodeInterface;

class CmsRepository extends WrappedManagedRepository implements CmsRepositoryInterface {

	private $rootCmsNode;

	private $nodeFactory;

	private $cmsNodes = [];

	public function __construct(ManagedRepositoryInterface $baseRepository, RepositoryCmsNodeFactory $nodeFactory) {
		parent::__construct($baseRepository);
		$this->nodeFactory = $nodeFactory;
		$this->initialize();
	}

	protected function initialize() {
		$nodes = $this->getCmsNodeFactory()->getRepositoryCmsNodes($this);
		$isRootNode = true;
		foreach($nodes as $node) {
			$this->registerCmsNode($node, $isRootNode);
			$isRootNode = false;
		}
	}

	public function getCmsSlug() {
		return $this->getName();
	}

	protected function getCmsNodeFactory() {
		return $this->nodeFactory;
	}

	public function getRootCmsNode() {
		return $this->rootCmsNode;
	}

	public function hasCmsNode($name) {
		return isset($this->cmsNodes[$name]);
	}

	public function getCmsNode($name) {
		if(!$this->hasCmsNode($name)) {
			throw new \InvalidArgumentException(sprintf('Node %s is not registered.', $name));
		}
		return $this->cmsNodes[$name];
	}

	public function getCmsNodes() {
		return $this->cmsNodes;
	}

	public function registerCmsNode(ControllerNodeInterface $node, $isRootNode = false) {
		$name = $node->getActionName();
		$this->cmsNodes[$name] = $node;
		if($isRootNode) {
			$this->rootCmsNode = $node;
		}
	}

}