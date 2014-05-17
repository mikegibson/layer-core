<?php

namespace Layer\Admin\Data;

use Layer\Data\Paginator\Paginator;
use Layer\Data\Paginator\PaginatorRequestInterface;
use Layer\Data\Paginator\PaginatorResult;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminIndexPaginator extends Paginator {

	protected $generator;

	protected $action = 'index';

	/**
	 * @param PaginatorResult $result
	 * @param PaginatorRequestInterface $request
	 * @param UrlGeneratorInterface $generator
	 */
	public function __construct(
		PaginatorResult $result,
		PaginatorRequestInterface $request,
		UrlGeneratorInterface $generator
	) {
		parent::__construct($result, $request);
		$this->generator = $generator;
	}

	public function getUrl($page = 1, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
		$repository = $this->result->getRepository();
		$params = [
			'action' => $this->action,
			'namespace' => $repository->getNamespace(),
			'type' => $repository->getBasename(),
			'page' => $page
		];
		return $this->generator->generate('admin_scaffold', $params, $referenceType);
	}

}