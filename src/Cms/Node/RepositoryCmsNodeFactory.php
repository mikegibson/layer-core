<?php

namespace Layer\Cms\Node;

use Layer\Action\ActionInterface;
use Layer\Cms\Action\RepositoryCmsActionFactoryInterface;
use Layer\Cms\Data\CmsRepositoryInterface;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;

class RepositoryCmsNodeFactory implements RepositoryCmsNodeFactoryInterface {

	/**
	 * @var \Layer\Node\ControllerNodeInterface
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

		$rootNode = null;

		foreach($this->actionFactories as $factory) {
			if($factory->isRepositoryEligible($repository)) {
				$actions[] = $factory->createAction($repository);
			}
		}

		if($actions) {
			$name = $repository->getCmsSlug();
			$label = $repository->queryMetadata('getEntityHumanName', ['plural' => true, 'capitalize' => true]);
			$rootAction = array_shift($actions);
			$repositoryBaseNode = $this->createNodeFromAction($rootAction);
			$nodes[] = $repositoryRoot = $this->rootCmsNode->wrapChildNode($repositoryBaseNode, $name, $label);
			foreach($actions as $action) {
				$nodes[] = $repositoryRoot->wrapChildNode($this->createNodeFromAction($action));
			}

		}

		return $nodes;
	}

	/**
	 * @param ActionInterface $action
	 * @return ControllerNode
	 */
	protected function createNodeFromAction(ActionInterface $action) {
		return new ControllerNode('cms', $action);
	}

}