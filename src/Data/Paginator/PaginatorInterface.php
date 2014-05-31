<?php

namespace Layer\Data\Paginator;

/**
 * Interface PaginatorInterface
 *
 * @package Layer\Data\Paginator
 */
interface PaginatorInterface extends TableDataInterface {

	/**
	 * @return array
	 */
	public function getData();

	/**
	 * @return int
	 */
	public function getTotal();

	/**
	 * @return int
	 */
	public function getCurrentPage();

	/**
	 * @return int
	 */
	public function getPerPage();

	/**
	 * @return int
	 */
	public function getPageCount();

	/**
	 * @return string|null
	 */
	public function getSortKey();

	/**
	 * @return string|null
	 */
	public function getDirection();

}