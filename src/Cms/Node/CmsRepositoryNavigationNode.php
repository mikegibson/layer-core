<?php

namespace Sentient\Cms\Node;

use Sentient\Cms\Data\CmsRepositoryInterface;
use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\ControllerNodeListNode;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CmsRepositoryNavigationNode extends ControllerNodeListNode {

	private $repository;

	/**
	 * @var \Sentient\Node\ControllerNodeInterface
	 */
	private $currentControllerNode;

	public function __construct(
		CmsRepositoryInterface $repository,
		UrlGeneratorInterface $urlGenerator,
		ControllerNodeInterface $currentControllerNode = null,
		$routeName = 'cms'
	) {
		$this->repository = $repository;
		$rootNode = $repository->getRootCmsNode();
		$this->currentControllerNode = $currentControllerNode;
		parent::__construct($rootNode, $routeName, $urlGenerator);
	}

	protected function initialize() {
		$currentNode = $this->getCurrentControllerNode();
		$diff = [$currentNode->getActionName() => null];
		$repository = $this->getRepository();
		foreach(array_diff_key(['index', 'add'], $diff) as $action) {
			if($repository->hasCmsNode($action)) {
				$controllerNode = $repository->getCmsNode($action);
				$listNode = $this->createListNode($controllerNode);
				$this->wrapChildNode($listNode, $controllerNode->getActionName(), $controllerNode->getActionLabel(), false);
			}
		}
		parent::initialize();
		if($currentNode !== null) {
			$this->childNodes = array_diff_key($this->childNodes, $diff);
		}
	}

	protected function getRepository() {
		return $this->repository;
	}

	/**
	 * @return ControllerNodeInterface|null
	 */
	protected function getCurrentControllerNode() {
		return $this->currentControllerNode;
	}

}