<?php

namespace Sentient\Cms\Node;

use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Node\ControllerNodeInterface;

class CmsNodeRegistry {

	private $nodes = [];

	public function register(
		ManagedRepositoryInterface $repository,
		ControllerNodeInterface $node,
		$isRootNode = false
	) {
		$this->nodes[$repository->getClassName()][$node->getActionName()] = $node;
		if($isRootNode) {
			$this->nodes[$repository->getClassName()]['_root'] = $node;
		}
	}

	public function has(ManagedRepositoryInterface $repository, $actionName) {
		if(substr($actionName, 0, 1) === '_') {
			return false;
		}
		$className = $repository->getClassName();
		if(!isset($this->nodes[$className])) {
			return false;
		}
		return isset($this->nodes[$className][$actionName]);
	}

	public function get(ManagedRepositoryInterface $repository, $actionName) {
		if(!$this->has($repository, $actionName)) {
			throw new \InvalidArgumentException(sprintf(
				'Action %s does not exist for repository %s',
				$actionName,
				$repository->getName()
			));
		}
		return $this->nodes[$repository->getClassName()][$actionName];
	}

	public function getRoot(ManagedRepositoryInterface $repository) {
		$className = $repository->getClassName();
		if(!isset($this->nodes[$className]) || !isset($this->nodes[$className]['_root'])) {
			throw new \InvalidArgumentException(sprintf('No root node exists for repository %s', $repository->getName()));
		}
		return $this->nodes[$className]['_root'];
	}

}