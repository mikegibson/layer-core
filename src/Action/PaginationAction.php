<?php

namespace Sentient\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\Paginator\Paginator;
use Sentient\Data\Paginator\PaginatorRequest;
use Sentient\Data\Paginator\PaginatorResult;
use Symfony\Component\HttpFoundation\Request;

class PaginationAction implements ActionInterface {

	protected $template;

	protected $result;

	/**
	 * @var \Sentient\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param $template
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

	public function isDirectlyAccessible() {
		return true;
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function invoke(Request $request) {

		return [
			'repository' => $this->getRepository(),
			'paginator' => $this->createPaginator($request)
		];

	}

	protected function createPaginator(Request $request) {
		return new Paginator($this->getPaginatorResult($request), $this->createPaginatorRequest($request));
	}

	/**
	 * @return \Sentient\Data\|ManagedRepositoryInterface
	 */
	public function getRepository() {
		return $this->repository;
	}

	public function getPaginatorResult(Request $request) {
		if($this->result === null) {
			$this->result = new PaginatorResult($this->getRepository(), $this->createQueryBuilder($request));
		}
		return $this->result;
	}

	protected function createQueryBuilder(Request $request) {
		return $this->getRepository()->createQueryBuilder($this->getRepository()->getName());
	}

	protected function createPaginatorRequest(Request $request) {
		return new PaginatorRequest($request);
	}

}