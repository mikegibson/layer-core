<?php

namespace Layer\Paginator;

use Doctrine\ORM\QueryBuilder;
use Layer\Data\DataType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginatorTrait
 *
 * @package Layer\Paginator
 */
trait PaginatorTrait {

	/**
	 * @param Application $app
	 * @param DataType $dataType
	 * @param QueryBuilder $queryBuilder
	 * @param Request $request
	 * @param array $resultConfig
	 * @param array $requestConfig
	 * @return Paginator
	 */
	protected function _buildPaginator(
		Application $app,
		DataType $dataType,
		Request $request,
		QueryBuilder $queryBuilder = null,
		array $resultConfig = [],
		array $requestConfig = []
	) {

		$paginatorResult = $this->_getPaginatorResult($app, $dataType, $queryBuilder, $resultConfig);
		$paginatorRequest = $this->_getPaginatorRequest($request, $requestConfig);

		return $this->_getPaginator($paginatorResult, $paginatorRequest);
	}

	/**
	 * @param PaginatorRequest $request
	 * @param PaginatorResult $result
	 * @return Paginator
	 */
	protected function _getPaginator(
		PaginatorResult $result,
		PaginatorRequest $request
	) {
		return new Paginator($result, $request);
	}

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
	 * @param Application $app
	 * @param DataType $dataType
	 * @param QueryBuilder $queryBuilder
	 * @param array $config
	 * @return PaginatorResult
	 */
	protected function _getPaginatorResult(
		Application $app,
		DataType $dataType,
		QueryBuilder $queryBuilder = null,
		array $config = []
	) {
		return new PaginatorResult($app, $dataType, $queryBuilder, $config);
	}

}