<?php

namespace Layer\Data\Paginator;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DecoratedPaginator extends DecoratedTableData implements PaginatorInterface {

	/**
	 * @var PaginatorInterface
	 */
	private $basePaginator;

	/**
	 * @param PaginatorInterface $basePaginator
	 * @param TableDataDecoratorInterface $decorator
	 * @param PropertyAccessorInterface $propertyAccessor
	 */
	public function __construct(
		PaginatorInterface $basePaginator,
		TableDataDecoratorInterface $decorator,
		PropertyAccessorInterface $propertyAccessor
	) {
		$this->basePaginator = $basePaginator;
		parent::__construct($basePaginator, $decorator, $propertyAccessor);
	}

	/**
	 * @return int
	 */
	public function getTotal() {
		return $this->basePaginator->getTotal();
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->basePaginator->getCurrentPage();
	}

	/**
	 * @return int
	 */
	public function getPerPage() {
		return $this->basePaginator->getPerPage();
	}

	/**
	 * @return int
	 */
	public function getPageCount() {
		return $this->basePaginator->getPageCount();
	}

	/**
	 * @return string|null
	 */
	public function getSortKey() {
		return $this->basePaginator->getSortKey();
	}

	/**
	 * @return string|null
	 */
	public function getDirection() {
		return $this->basePaginator->getDirection();
	}

	/**
	 * @param null $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @return array
	 */
	public function getUrlParameters($page = null, $limit = null, $sortKey = null, $direction = null) {
		return $this->basePaginator->getUrlParameters($page, $limit, $sortKey, $direction);
	}

}