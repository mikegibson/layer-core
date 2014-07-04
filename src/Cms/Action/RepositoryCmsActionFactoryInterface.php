<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;

interface RepositoryCmsActionFactoryInterface {

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(ManagedRepositoryInterface $repository);

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return \Sentient\Action\ActionInterface
	 */
	public function createAction(ManagedRepositoryInterface $repository);

}