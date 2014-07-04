<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\TableData\TableDataDecoratorInterface;
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
	 * @param ManagedRepositoryInterface $repository
	 * @return bool
	 */
	public function isRepositoryEligible(ManagedRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		return !empty($crud->read);
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return \Sentient\Action\ActionInterface|void
	 */
	public function createAction(ManagedRepositoryInterface $repository) {
		return new IndexAction($repository, $this->propertyAccessor, $this->decorator);
	}

}