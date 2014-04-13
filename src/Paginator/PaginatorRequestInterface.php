<?php

namespace Layer\Paginator;

/**
 * Interface PaginatorRequestInterface
 *
 * @package Layer\Paginator
 */
interface PaginatorRequestInterface {

	/**
	 * @return int
	 */
	public function getPage();

	/**
	 * @return int
	 */
	public function getLimit();

	/**
	 * @return string|null
	 */
	public function getSortKey();

	/**
	 * @return string|null
	 */
	public function getDirection();

}