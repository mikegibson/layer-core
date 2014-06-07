<?php

namespace Layer\Cms\Action;

use Layer\Action\ActionInterface;
use Layer\Cms\Data\CmsRepositoryInterface;
use Layer\Cms\Data\Paginator;
use Layer\Data\Paginator\PaginatorRequest;
use Layer\Data\Paginator\PaginatorResult;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class IndexAction
 * @package Layer\Cms\Action
 * @Annotation
 */
class IndexAction implements ActionInterface {

	/**
	 * @var \Layer\Cms\Data\CmsRepositoryInterface
	 */
	private $repository;

	/**
	 * @param CmsRepositoryInterface $repository
	 */
	public function __construct(CmsRepositoryInterface $repository) {
		$this->repository = $repository;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'index';
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return sprintf('List %s', $this->repository->queryMetadata('getEntityHumanName', ['plural' => true]));
	}

	public function getTemplate() {
		return '@cms/view/index';
	}

	public function isVisible() {
		return true;
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function invoke(Request $request) {

		$repository = $this->getRepository();
		$queryBuilder = $repository->createQueryBuilder($repository->getName());
		$paginatorResult = new PaginatorResult($repository, $queryBuilder);
		$paginatorRequest = new PaginatorRequest($request);
		$paginator = new Paginator($paginatorResult, $paginatorRequest);

		return compact('repository', 'paginator');
	}

	/**
	 * @return CmsRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
	}

}