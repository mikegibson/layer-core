<?php

namespace Sentient\Data\Paginator;

/**
 * Interface PaginatorRequestInterface
 *
 * @package Sentient\Data\Paginator
 */
interface PaginatorRequestInterface {

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

	/**
	 * @param null $page
	 * @param null $limit
	 * @param null $sortKey
	 * @param null $direction
	 * @return array
	 */
	public function getUrlParameters($page = null, $limit = null, $sortKey = null, $direction = null);

}