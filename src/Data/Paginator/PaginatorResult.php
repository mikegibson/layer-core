<?php

namespace Layer\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Metadata\Annotation\FieldLabel;

/**
 * Class PaginatorQuery
 *
 * @package Layer\Data\Paginator
 */
class PaginatorResult implements PaginatorResultInterface {

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
	 * @param QueryBuilder $builder
	 * @return mixed
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
	 * @param QueryBuilder $queryBuilder
	 * @return QueryBuilder
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
	 * @param QueryBuilder $queryBuilder
	 * @return int
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