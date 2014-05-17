<?php

namespace Layer\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Layer\Data\ManagedRepositoryInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PaginatorTrait
 *
 * @package Layer\Data\Paginator
 */
trait PaginatorTrait {

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param Request $request
	 * @param UrlGeneratorInterface $generator
	 * @param QueryBuilder $queryBuilder
	 * @param array $resultConfig
	 * @param array $requestConfig
	 * @return mixed
	 */
	protected function _buildPaginator(
		ManagedRepositoryInterface $repository,
		Request $request,
		UrlGeneratorInterface $generator,
		QueryBuilder $queryBuilder = null,
		array $resultConfig = [],
		array $requestConfig = []
	) {

		$paginatorResult = $this->_getPaginatorResult($repository, $queryBuilder, $resultConfig);
		$paginatorRequest = $this->_getPaginatorRequest($request, $requestConfig);

		return $this->_getPaginator($paginatorResult, $paginatorRequest, $generator);
	}

	/**
	 * @param PaginatorResultInterface $result
	 * @param PaginatorRequestInterface $request
	 * @param UrlGeneratorInterface $generator
	 * @return mixed
	 */
	abstract protected function _getPaginator(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request,
		UrlGeneratorInterface $generator
	);

	/**
	 * @param Request $request
	 * @param array $config
	 * @return PaginatorRequest
	 */
	protected function _getPaginatorRequest(
		Request $request,
		array $config = []
	) {
		return new PaginatorRequest($request, $config);
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param QueryBuilder $queryBuilder
	 * @param array $config
	 * @return PaginatorResult
	 */
	protected function _getPaginatorResult(
		ManagedRepositoryInterface $repository,
		QueryBuilder $queryBuilder = null,
		array $config = []
	) {
		return new PaginatorResult($repository, $queryBuilder, $config);
	}

}