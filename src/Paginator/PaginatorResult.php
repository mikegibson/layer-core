<?php

namespace Layer\Paginator;

use Illuminate\Database\Query\Builder;
use Layer\Application;
use Layer\Data\DataType;
use Layer\Utility\SetPropertiesTrait;

/**
 * Class PaginatorQuery
 *
 * @package Layer\Paginator
 */
class PaginatorResult implements PaginatorResultInterface {

	use SetPropertiesTrait;

	/**
	 * @var \Layer\Application
	 */
	protected $app;

	/**
	 * @var \Layer\Data\DataType
	 */
	protected $dataType;

	/**
	 * @var \Layer\Data\QueryBuilder
	 */
	protected $builder;

	/**
	 * @var int
	 */
	public $limit = 10;

	/**
	 * @var int
	 */
	public $maxLimit = 100;

	/**
	 * @param Application $app
	 * @param DataType $dataType
	 * @param Builder $builder
	 * @param array $config
	 */
	public function __construct(Application $app, DataType $dataType, Builder $builder = null, array $config = []) {

		$this->_setProperties($config);
		$this->app = $app;
		$this->dataType = $dataType;
		if ($builder !== null) {
			$this->setQuery($builder);
		}
	}

	/**
	 * @param Builder $builder
	 */
	public function setQuery(Builder $builder) {

		$this->query = $builder;
	}

	/**
	 * @return Builder
	 */
	public function getQuery() {

		return $this->query;
	}

	/**
	 * @return array
	 */
	public function getColumns() {

		$columns = [];
		foreach ($this->dataType->fields() as $name => $field) {
			if (!$field->visible || !$field->important) {
				continue;
			}
			$columns[$name] = $field->label;
		}

		return $columns;
	}

	/**
	 * @param int $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @param array $columns
	 * @param Builder $builder
	 * @return mixed
	 */
	public function getData($page = 1, $limit = null, $sortKey = null, $direction = null, $columns = ['*'], Builder $builder = null) {

		return $this->paginateQuery($page, $limit, $sortKey, $direction, $builder)->get($columns);
	}

	/**
	 * @param int $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @param QueryBuilder $builder
	 * @return QueryBuilder
	 */
	public function paginateQuery($page = 1, $limit = null, $sortKey = null, $direction = null, QueryBuilder $builder = null) {

		$builder = $this->_getQuery($builder);

		$limit = (int)$limit;
		if ($limit < 1) {
			$limit = $this->limit;
		}

		$limit = min([$limit, $this->maxLimit]);

		return $builder->forPage($page, $limit);
	}

	/**
	 * @param Builder $builder
	 * @return int
	 */
	public function getCount(Builder $builder = null) {

		$builder = $this->_getQuery($builder);

		return $builder->count();
	}

	/**
	 * @return DataType
	 */
	public function getDataType() {
		return $this->dataType;
	}

	/**
	 * @param Builder $builder
	 * @return Builder
	 * @throws \Exception
	 */
	protected function _getQuery(Builder $builder = null) {

		if ($builder === null) {
			if ($this->query === null) {
				throw new \Exception('No query object specified!');
			}
			$builder = $this->query;
		}

		return $builder;
	}

}