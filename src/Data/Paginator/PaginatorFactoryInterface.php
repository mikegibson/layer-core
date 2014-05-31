<?php

namespace Layer\Data\Paginator;

use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

interface PaginatorFactoryInterface {

	public function createPaginator(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request
	);

	function createPaginatorRequest(Request $request);

	public function createPaginatorResult(ManagedRepositoryInterface $repository);

}