<?php

namespace Sentient\Cms\Action;

use Sentient\Action\ActionInterface;
use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RepositoryActionAdapter implements ActionInterface {

	/**
	 * @var \Sentient\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @var RepositoryActionInterface
	 */
	private $action;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param RepositoryActionInterface $action
	 */
	public function __construct(ManagedRepositoryInterface $repository, RepositoryActionInterface $action) {
		$this->repository = $repository;
		$this->action = $action;
	}

	public function getName() {
		return $this->action->getName();
	}

	public function getLabel() {
		return $this->action->getLabel($this->repository);
	}

	public function isVisible() {
		return !$this->action->isEntityRequired();
	}

	public function isDirectlyAccessible() {
		return !$this->action->isEntityRequired();
	}

	public function getTemplate() {
		return $this->action->getTemplate($this->repository);
	}

	public function invoke(Request $request) {
		$result = $this->action->invoke($this->repository, $request);
		if(is_array($result)) {
			$result['repository'] = $this->repository;
		}
		return $result;
	}

}