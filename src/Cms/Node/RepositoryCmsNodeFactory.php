<?php

namespace Sentient\Cms\Node;

use Sentient\Cms\Action\RepositoryActionAdapter;
use Sentient\Cms\Action\RepositoryActionInterface;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Node\ControllerNode;
use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\NodeInterface;

class RepositoryCmsNodeFactory implements RepositoryCmsNodeFactoryInterface {

	/**
	 * @var \Sentient\Node\ControllerNodeInterface
	 */
	private $rootCmsNode;

	/**
	 * @var RepositoryActionInterface[]
	 */
	private $actions = [];

	/**
	 * @param ControllerNodeInterface $rootCmsNode
	 * @param RepositoryActionInterface[] $actions
	 */
	public function __construct(ControllerNodeInterface $rootCmsNode, array $actions = []) {
		$this->rootCmsNode = $rootCmsNode;
		foreach($actions as $action) {
			$this->registerAction($action);
		}
	}

	/**
	 * @param RepositoryActionInterface $action
	 */
	public function registerAction(RepositoryActionInterface $action) {
		$this->actions[] = $action;
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return array
	 * @throws \RuntimeException
	 */
	public function createNodes(ManagedRepositoryInterface $repository) {

		$actions = $nodes = [];

		foreach($this->actions as $action) {
			if($action->isRepositoryEligible($repository)) {
				$actions[] = $action;
			}
		}

		if($actions) {
			$parentNode = $this->rootCmsNode;
			$name = $repository->queryMetadata('getCmsEntitySlug');
			$label = $repository->queryMetadata('getEntityHumanName', ['plural' => true, 'capitalize' => true]);
			$rootAction = array_shift($actions);
			$repositoryBaseNode = $this->createNodeFromAction($repository, $rootAction);
			if($nodePath = $repository->queryMetadata('getCmsNodePath')) {
				$parts = explode(NodeInterface::SEPARATOR, $nodePath);
				$name = array_pop($parts);
				if($parts) {
					$nodePath = implode(NodeInterface::SEPARATOR, $parts);
					try {
						$parentNode = $parentNode->getDescendent($nodePath);
					} catch(\InvalidArgumentException $e) {
						throw new \RuntimeException(sprintf('CMS node path %s is not valid.', $nodePath));
					}
				}
			}
			$nodes[] = $repositoryRoot = $parentNode->wrapChild($repositoryBaseNode, $name, $label);
			foreach($actions as $action) {
				$nodes[] = $repositoryRoot->wrapChild($this->createNodeFromAction($repository, $action), $action->getName());
			}

		}

		return $nodes;
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param RepositoryActionInterface $action
	 * @return ControllerNode
	 */
	protected function createNodeFromAction(ManagedRepositoryInterface $repository, RepositoryActionInterface $action) {
		return new ControllerNode(new RepositoryActionAdapter($repository, $action));
	}

}