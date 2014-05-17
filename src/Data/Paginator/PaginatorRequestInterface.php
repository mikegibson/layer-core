<?php

namespace Layer\Data\Paginator;

/**
 * Interface PaginatorRequestInterface
 *
 * @package Layer\Data\Paginator
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

}