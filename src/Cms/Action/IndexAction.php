<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\Paginator\DecoratedPaginator;
use Sentient\Data\Paginator\Paginator;
use Sentient\Data\Paginator\PaginatorInterface;
use Sentient\Data\Paginator\PaginatorRequest;
use Sentient\Data\Paginator\PaginatorResult;
use Sentient\Data\TableData\TableDataDecoratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IndexAction implements RepositoryActionInterface {

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $decorator;

	/**
	 * @var string
	 */
	private $template;

	/**
	 * @param PropertyAccessorInterface $propertyAccessor
	 * @param TableDataDecoratorInterface $decorator
	 * @param string $template
	 */
	public function __construct(
		PropertyAccessorInterface $propertyAccessor,
		TableDataDecoratorInterface $decorator,
		$template = '@cms/view/index'
	) {
		$this->propertyAccessor = $propertyAccessor;
		$this->decorator = $decorator;
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'index';
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return string
	 */
	public function getLabel(ManagedRepositoryInterface $repository) {
		return sprintf('List %s', $repository->queryMetadata('getEntityHumanName', ['plural' => true]));
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @return string
	 */
	public function getTemplate(ManagedRepositoryInterface $repository) {
		return $this->template;
	}

	public function isEntityRequired() {
		return false;
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
	 * @param Request $request
	 * @return array
	 */
	public function invoke(ManagedRepositoryInterface $repository, Request $request) {
		return [
			'paginator' => $this->createPaginator($repository, $request)
		];
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param Request $request
	 * @return PaginatorInterface
	 */
	protected function createPaginator(ManagedRepositoryInterface $repository, Request $request) {
		return $this->decoratePaginator(new Paginator(
			$this->getPaginatorResult($repository, $request),
			$this->createPaginatorRequest($repository, $request)
		));
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param Request $request
	 * @return PaginatorResult
	 */
	protected function getPaginatorResult(ManagedRepositoryInterface $repository, Request $request) {
		return new PaginatorResult($repository, $this->createQueryBuilder($repository, $request));
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param Request $request
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	protected function createQueryBuilder(ManagedRepositoryInterface $repository, Request $request) {
		return $repository->createQueryBuilder($repository->getName());
	}

	/**
	 * @param Request $request
	 * @return PaginatorRequest
	 */
	protected function createPaginatorRequest(ManagedRepositoryInterface $repository, Request $request) {
		return new PaginatorRequest($request);
	}

	/**
	 * @param PaginatorInterface $paginator
	 * @return PaginatorInterface
	 */
	protected function decoratePaginator(PaginatorInterface $paginator) {
		return new DecoratedPaginator($paginator, $this->decorator, $this->propertyAccessor);
	}

}