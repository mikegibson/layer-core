<?php

namespace Sentient\Cms\Node;

use Sentient\Data\ManagedRepositoryInterface;
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
		ManagedRepositoryInterface $repository,
		UrlGeneratorInterface $urlGenerator,
		ControllerNodeInterface $currentControllerNode = null,
		$routeName = 'cms'
	) {
		$rootNode = $repository->queryMetadata('getRootCmsNode');
		parent::__construct($rootNode, $routeName, $urlGenerator);
		$this->repository = $repository;
		$this->currentControllerNode = $currentControllerNode;
		$diff = $currentControllerNode === null ? [] : [$currentControllerNode->getActionName() => null];
		foreach(array_diff(['index', 'add'], array_keys($diff)) as $action) {
			if(!$this->hasChildNode($action) && $repository->queryMetadata('hasCmsNode', compact('action'))) {
				$node = $repository->queryMetadata('getCmsNode', compact('action'));
				$listNode = $this->createListNode($node);
				$this->wrapChildNode($listNode, $action, $node->getActionLabel(), false);
			}
		}
		$this->childNodes = array_diff_key($this->childNodes, $diff);
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