<?php

namespace Sentient\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Sentient\Data\ManagedRepositoryInterface;

/**
 * Class PaginatorQuery
 *
 * @package Sentient\Data\Paginator
 */
class PaginatorResult implements PaginatorResultInterface {

	/**
	 * @var \Sentient\Data\ManagedRepositoryInterface
	 */
	protected $repository;

	/**
	 * @var \Doctrine\ORM\QueryBuilder
	 */
	protected $queryBuilder;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param QueryBuilder $queryBuilder
	 */
	public function __construct(ManagedRepositoryInterface $repository, QueryBuilder $queryBuilder) {
		$this->repository = $repository;
		$this->queryBuilder = $queryBuilder;
	}

	/**
	 * @return array
	 */
	public function getColumns() {
		return $this->repository->queryMetadata('getVisiblePropertyLabels', ['important' => true]);
	}

	/**
	 * @param int $page
	 * @param null $perPage
	 * @param null $sortKey
	 * @param null $direction
	 * @param array $columns
	 * @return array
	 */
	public function getData(
		$page = 1,
		$perPage = null,
		$sortKey = null,
		$direction = null,
		$columns = ['*']
	) {

		$query = $this->getQuery($page, $perPage, $sortKey, $direction);
		return $query->getResult();
	}

	/**
	 * @param int $page
	 * @param null $perPage
	 * @param null $sortKey
	 * @param null $direction
	 * @return \Doctrine\ORM\Query
	 */
	public function getQuery(
		$page = 1,
		$perPage = null,
		$sortKey = null,
		$direction = null
	) {

		$query = $this->queryBuilder->getQuery();
		$query->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);

		return $query;
	}

	/**
	 * @return int|mixed
	 */
	public function getCount() {

		$alias = $this->repository->getName();

		return $this->queryBuilder
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

}