<?php

namespace Layer\Paginator;

use Layer\View\Table\TableDataInterface;

/**
 * Interface PaginatorInterface
 *
 * @package Layer\Paginator
 */
interface PaginatorInterface extends TableDataInterface {

	/**
	 * @param int $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
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
	 * @return string|null
	 */
	public function getSortKey();

	/**
	 * @return string|null
	 */
	public function getDirection();

}