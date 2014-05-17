<?php

namespace Layer\Data\Paginator;

/**
 * Interface PaginatorQueryInterface
 *
 * @package Layer\Data\Paginator
 */
interface PaginatorResultInterface extends TableDataInterface {

	/**
	 * @param int $page
	 * @param null $perPage
	 * @param null $sortKey
	 * @param null $direction
	 * @return mixed
	 */
	public function getData($page = 1, $perPage = null, $sortKey = null, $direction = null);

	/**
	 * @return int
	 */
	public function getCount();

}