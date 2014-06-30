<?php

namespace Sentient\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginatorTrait
 *
 * @package Sentient\Data\Paginator
 */
trait PaginatorTrait {

	/**
	 * @param Request $request
	 * @param QueryBuilder $queryBuilder
	 * @param array $resultConfig
	 * @param array $requestConfig
	 * @return mixed
	 */
	protected function buildPaginator(
		QueryBuilder $queryBuilder = null,
		Request $request,
		array $resultConfig = [],
		array $requestConfig = []
	) {

		$paginatorResult = $this->getPaginatorResult($queryBuilder, $resultConfig);
		$paginatorRequest = $this->getPaginatorRequest($request, $requestConfig);

		return $this->getPaginator($paginatorResult, $paginatorRequest);
	}

	/**
	 * @param PaginatorResultInterface $result
	 * @param PaginatorRequestInterface $request
	 * @return mixed
	 */
	abstract protected function getPaginator(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request
	);

	/**
	 * @return \Sentient\Data\ManagedRepositoryInterface
	 */
	abstract protected function getRepository();

	/**
	 * @param Request $request
	 * @param array $config
	 * @return PaginatorRequest
	 */
	protected function getPaginatorRequest(
		Request $request,
		array $config = []
	) {
		return new PaginatorRequest($request, $config);
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param array $config
	 * @return PaginatorResult
	 */
	protected function getPaginatorResult(
		QueryBuilder $queryBuilder = null,
		array $config = []
	) {
		return new PaginatorResult($this->getRepository(), $queryBuilder, $config);
	}

}