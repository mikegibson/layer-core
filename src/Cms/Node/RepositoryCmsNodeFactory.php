<?php

namespace Sentient\Cms\Node;

use Sentient\Action\ActionInterface;
use Sentient\Cms\Action\RepositoryCmsActionFactoryInterface;
use Sentient\Cms\Data\CmsRepositoryInterface;
use Sentient\Node\ControllerNode;
use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\NodeInterface;

class RepositoryCmsNodeFactory implements RepositoryCmsNodeFactoryInterface {

	/**
	 * @var \Sentient\Node\ControllerNodeInterface
	 */
	private $rootCmsNode;

	/**
	 * @var RepositoryCmsActionFactoryInterface[]
	 */
	private $actionFactories = [];

	/**
	 * @param ControllerNodeInterface $rootCmsNode
	 * @param RepositoryCmsActionFactoryInterface[] $actionFactories
	 */
	public function __construct(ControllerNodeInterface $rootCmsNode, array $actionFactories = []) {
		$this->rootCmsNode = $rootCmsNode;
		foreach($actionFactories as $factory) {
			$this->registerActionFactory($factory);
		}
	}

	/**
	 * @param RepositoryCmsActionFactoryInterface $actionFactory
	 */
	public function registerActionFactory(RepositoryCmsActionFactoryInterface $actionFactory) {
		$this->actionFactories[] = $actionFactory;
	}

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return array|null
	 */
	public function getRepositoryCmsNodes(CmsRepositoryInterface $repository) {

		$actions = $nodes = [];

		foreach($this->actionFactories as $factory) {
			if($factory->isRepositoryEligible($repository)) {
				$actions[] = $factory->createAction($repository);
			}
		}

		if($actions) {
			$parentNode = $this->rootCmsNode;
			$name = $repository->getCmsSlug();
			$label = $repository->queryMetadata('getEntityHumanName', ['plural' => true, 'capitalize' => true]);
			$rootAction = array_shift($actions);
			$repositoryBaseNode = $this->createNodeFromAction($rootAction);
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
			$nodes[] = $repositoryRoot = $parentNode->wrapChildNode($repositoryBaseNode, $name, $label);
			foreach($actions as $action) {
				$nodes[] = $repositoryRoot->wrapChildNode($this->createNodeFromAction($action), $action->getName());
			}

		}

		return $nodes;
	}

	/**
	 * @param ActionInterface $action
	 * @return ControllerNode
	 */
	protected function createNodeFromAction(ActionInterface $action) {
		return new ControllerNode($action);
	}

}