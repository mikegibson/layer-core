<?php

namespace Layer\Action;

use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Paginator\Paginator;
use Layer\Data\Paginator\PaginatorRequest;
use Layer\Data\Paginator\PaginatorResult;
use Symfony\Component\HttpFoundation\Request;

class PaginationAction implements ActionInterface {

	protected $template;

	/**
	 * @var \Layer\Cms\Data\CmsRepositoryInterface|\Layer\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @param CmsRepositoryInterface $repository
	 */
	public function __construct(ManagedRepositoryInterface $repository, $template) {
		$this->repository = $repository;
		$this->template = $template;
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
		return $this->template;
	}

	public function isVisible() {
		return true;
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function invoke(Request $request) {

		return [
			'repository' => $this->getRepository(),
			'paginator' => $this->getPaginator($request)
		];

	}

	protected function getPaginator(Request $request) {
		return new Paginator($this->getPaginatorResult(), $this->getPaginatorRequest($request));
	}

	/**
	 * @return \Layer\Data\|ManagedRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
	}

	protected function getQueryBuilder() {
		return $this->getRepository()->createQueryBuilder($this->getRepository()->getName());
	}

	protected function getPaginatorResult() {
		return new PaginatorResult($this->getRepository(), $this->getQueryBuilder());
	}

	protected function getPaginatorRequest(Request $request) {
		return new PaginatorRequest($request);
	}

}