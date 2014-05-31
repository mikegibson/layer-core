<?php

namespace Layer\Data\Paginator;

use Doctrine\ORM\QueryBuilder;
use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class PaginatorFactory implements PaginatorFactoryInterface {

	public function createPaginator(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request
	) {

	}

	function createPaginatorRequest(Request $request) {
		return new PaginatorRequest($request);
	}

	public function createPaginatorResult(ManagedRepositoryInterface $repository, QueryBuilder $queryBuilder = null) {
		if($queryBuilder === null) {
			$queryBuilder = $repository->createQueryBuilder();
		}
		return new PaginatorResult($repository, $queryBuilder);
	}

}