<?php

namespace Sentient\Cms\Action;

use Sentient\Cms\Data\CmsRepositoryInterface;

interface RepositoryCmsActionFactoryInterface {

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(CmsRepositoryInterface $repository);

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return \Sentient\Action\ActionInterface
	 */
	public function createAction(CmsRepositoryInterface $repository);

}