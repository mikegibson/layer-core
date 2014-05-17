<?php

namespace Layer\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Utility\SetPropertiesTrait;

/**
 * Class PaginatorQuery
 *
 * @package Layer\Data\Paginator
 */
class PaginatorResult implements PaginatorResultInterface {

	use SetPropertiesTrait;

	/**
	 * @var \Layer\Application
	 */
	protected $app;

	/**
	 * @var \Layer\Data\ManagedRepositoryInterface
	 */
	protected $repository;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param QueryBuilder $queryBuilder
	 * @param array $config
	 */
	public function __construct(ManagedRepositoryInterface $repository, QueryBuilder $queryBuilder = null, array $config = []) {

		$this->_setProperties($config);
		$this->repository = $repository;
		if($queryBuilder === null) {
			$queryBuilder = $repository->createQueryBuilder();
		}
		$this->setQueryBuilder($queryBuilder);
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 */
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
	}

	/**
	 * @param int $page
	 * @param null $perPage
	 * @param null $sortKey
	 * @param null $direction
	 * @param array $columns
	 * @param QueryBuilder $builder
	 * @return mixed
	 */
	public function getData(
		$page = 1,
		$perPage = null,
		$sortKey = null,
		$direction = null,
		$columns = ['*'],
		QueryBuilder $builder = null
	) {

		return $this->getQuery($page, $perPage, $sortKey, $direction)->getResult();
	}

	/**
	 * @param int $page
	 * @param null $perPage
	 * @param null $sortKey
	 * @param null $direction
	 * @param QueryBuilder $queryBuilder
	 * @return QueryBuilder
	 */
	public function getQuery(
		$page = 1,
		$perPage = null,
		$sortKey = null,
		$direction = null,
		QueryBuilder $queryBuilder = null
	) {

		$query = $this->_getQueryBuilder($queryBuilder)->getQuery();
		$query->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

		return $query;
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @return int
	 */
	public function getCount(QueryBuilder $queryBuilder = null, $alias = null) {

		if($alias === null) {
			$alias = $this->repository->getName();
		}

		return $this->_getQueryBuilder($queryBuilder)
			->select("COUNT({$alias})")
			->getQuery()
			->getSingleScalarResult();
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	public function getRepository() {
		return $this->repository;
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @return QueryBuilder
	 */
	protected function _getQueryBuilder(QueryBuilder $queryBuilder = null) {

		return ($queryBuilder === null) ? $this->queryBuilder : $queryBuilder;

	}

}