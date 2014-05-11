<?php

namespace Layer\Paginator;

use Doctrine\ORM\QueryBuilder;
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

	protected $dataType;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;

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
	 * @param QueryBuilder $queryBuilder
	 * @param array $config
	 */
	public function __construct(Application $app, DataType $dataType, QueryBuilder $queryBuilder = null, array $config = []) {

		$this->_setProperties($config);
		$this->app = $app;
		$this->dataType = $dataType;
		if($queryBuilder === null) {
			$queryBuilder = $dataType->createQueryBuilder();
		}
		$this->setQueryBuilder($queryBuilder);
	}

	public function setQueryBuilder(QueryBuilder $queryBuilder) {
		$this->queryBuilder = $queryBuilder;
	}

	/**
	 * @return array
	 */
	public function getColumns() {

		return [
			'title' => 'Title',
			'content' => 'Content'
		];
/*
		$columns = [];
		foreach ($this->dataType->fields() as $name => $field) {
			if (!$field->visible || !$field->important) {
				continue;
			}
			$columns[$name] = $field->label;
		}

		return $columns;*/
	}

	/**
	 * @param int $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @param array $columns
	 * @param QueryBuilder $queryBuilder
	 * @return mixed
	 */
	public function getData(
		$page = 1,
		$limit = null,
		$sortKey = null,
		$direction = null,
		$columns = ['*'],
		QueryBuilder $builder = null
	) {

		return $this->getQuery($page, $limit, $sortKey, $direction)->getResult();
	}

	/**
	 * @param int $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @param QueryBuilder $queryBuilder
	 * @return QueryBuilder
	 */
	public function getQuery(
		$page = 1,
		$limit = null,
		$sortKey = null,
		$direction = null,
		QueryBuilder $queryBuilder = null
	) {

		$limit = (int)$limit;
		if ($limit < 1) {
			$limit = $this->limit;
		}

		$query = $this->_getQueryBuilder($queryBuilder)->getQuery();
		$query->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

		return $query;
	}

	/**
	 * @param Builder $queryBuilder
	 * @return int
	 */
	public function getCount(QueryBuilder $queryBuilder = null) {

		return $this->_getQueryBuilder($queryBuilder)
			->select("COUNT({$this->dataType->name})")
			->getQuery()
			->getSingleScalarResult();
	}

	public function getDataType() {
		return $this->dataType;
	}

	/**
	 * @param Builder $queryBuilder
	 * @return Builder
	 * @throws \Exception
	 */
	protected function _getQueryBuilder(QueryBuilder $queryBuilder = null) {

		return ($queryBuilder === null) ? $this->queryBuilder : $queryBuilder;

	}

}