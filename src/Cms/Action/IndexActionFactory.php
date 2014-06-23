<?php

namespace Layer\Cms\Action;

use Layer\Cms\Data\CmsRepositoryInterface;
use Layer\Data\TableData\TableDataDecoratorInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IndexActionFactory implements RepositoryCmsActionFactoryInterface {

	private $decorator;

	private $propertyAccessor;

	public function __construct(
		TableDataDecoratorInterface $decorator,
		PropertyAccessorInterface $propertyAccessor
	) {
		$this->decorator = $decorator;
		$this->propertyAccessor = $propertyAccessor;
	}

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(CmsRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		return !empty($crud->read);
	}

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return \Layer\Action\ActionInterface|void
	 */
	public function createAction(CmsRepositoryInterface $repository) {
		return new IndexAction($repository, $this->propertyAccessor, $this->decorator);
	}

}