<?php

namespace Layer\Paginator;

use Illuminate\Database\Query\Builder;
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
	 * @param DataType $dataType
	 * @param Request $request
	 * @param QueryBuilder $query
	 * @param array $requestConfig
	 * @param array $resultConfig
	 * @return Paginator
	 */
	protected function _buildPaginator(
		Application $app,
		DataType $dataType,
		Request $request,
		Builder $query = null,
		array $requestConfig = [],
		array $resultConfig = []
	) {

		$paginatorRequest = $this->_getPaginatorRequest($request, $requestConfig);
		$paginatorResult = $this->_getPaginatorResult($app, $dataType, $query, $resultConfig);

		return $this->_getPaginator($paginatorRequest, $paginatorResult);
	}

	/**
	 * @param DataType $dataType
	 * @param PaginatorRequest $request
	 * @param PaginatorResult $result
	 * @return Paginator
	 */
	protected function _getPaginator(PaginatorRequest $request, PaginatorResult $result) {
		return new Paginator($request, $result);
	}

	/**
	 * @param Request $request
	 * @param array $config
	 * @return PaginatorRequest
	 */
	protected function _getPaginatorRequest(Request $request, array $config = []) {
		return new PaginatorRequest($request, $config);
	}

	/**
	 * @param DataType $dataType
	 * @param QueryBuilder $query
	 * @param array $config
	 * @return PaginatorResult
	 */
	protected function _getPaginatorResult(Application $app, DataType $dataType, Builder $query = null, array $config = []) {
		$query = $this->_getPaginatorQuery($dataType, $query);
		return new PaginatorResult($app, $dataType, $query, $config);
	}

	/**
	 * @param DataType $dataType
	 * @param QueryBuilder $query
	 * @return Builder
	 */
	protected function _getPaginatorQuery(DataType $dataType, Builder $query = null) {
		return ($query === null) ? $dataType->query() : $query;
	}

}