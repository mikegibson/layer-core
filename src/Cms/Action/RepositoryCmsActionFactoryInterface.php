<?php

namespace Layer\Cms\Action;

use Layer\Cms\Data\CmsRepositoryInterface;

interface RepositoryCmsActionFactoryInterface {

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(CmsRepositoryInterface $repository);

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return \Layer\Action\ActionInterface
	 */
	public function createAction(CmsRepositoryInterface $repository);

}