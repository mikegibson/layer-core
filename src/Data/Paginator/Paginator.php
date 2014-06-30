<?php

namespace Sentient\Data\Paginator;

/**
 * Class Paginator
 *
 * @package Controller\Paginator
 */
class Paginator implements PaginatorInterface {

	/**
	 * @var PaginatorResultInterface
	 */
	private $result;

	/**
	 * @var \Sentient\Data\Paginator\PaginatorRequestInterface
	 */
	private $request;

	/**
	 * @var int
	 */
	protected $defaultPerPage = 10;

	/**
	 * @var int
	 */
	protected $maxPerPage = 100;

	/**
	 * @param PaginatorRequestInterface $request
	 * @param PaginatorResultInterface $result
	 */
	public function __construct(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request
	) {
		$this->result = $result;
		$this->request = $request;
	}

	/**
	 * @return array
	 */
	public function getColumns() {

		return $this->result->getColumns();
	}

	/**
	 * @return array
	 */
	public function getData() {

		return $this->result->getData($this->getCurrentPage(), $this->getPerPage(), $this->getSortKey(), $this->getDirection());
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {

		return $this->request->getCurrentPage();
	}

	/**
	 * @return int
	 */
	public function getPerPage() {

		return min((int) $this->request->getPerPage() ?: $this->defaultPerPage, $this->maxPerPage);
	}

	/**
	 * @return string|null
	 */
	public function getSortKey() {

		return $this->request->getSortKey();
	}

	/**
	 * @return string|null
	 */
	public function getDirection() {

		return $this->request->getDirection();
	}

	/**
	 * @return int
	 */
	public function getTotal() {

		return $this->result->getCount();
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return (int)ceil($this->getTotal() / $this->getPerPage());
	}

	/**
	 * @return bool
	 */
	public function hasNext() {
		return ($this->getPageCount() > $this->getCurrentPage());
	}

	/**
	 * @return bool
	 */
	public function hasPrev() {
		return ($this->getCurrentPage() > 1);
	}

	/**
	 * @param $page
	 * @return bool
	 */
	public function hasPage($page) {
		return ($this->getPageCount() >= (int)$page);
	}

	/**
	 * @return PaginatorRequestInterface
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return PaginatorResultInterface
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * @param null $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @return array
	 */
	public function getUrlParameters($page = null, $limit = null, $sortKey = null, $direction = null) {
		return $this->request->getUrlParameters($page, $limit, $sortKey, $direction);
	}

}