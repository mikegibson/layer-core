<?php

namespace Sentient\Data\Paginator;

use Sentient\Data\TableData\TableDataInterface;

/**
 * Interface PaginatorInterface
 *
 * @package Sentient\Data\Paginator
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

	/**
	 * @param null $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @return array
	 */
	public function getUrlParameters($page = null, $limit = null, $sortKey = null, $direction = null);

}