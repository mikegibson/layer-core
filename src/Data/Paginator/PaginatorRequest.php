<?php

namespace Layer\Data\Paginator;

use Layer\Utility\SetPropertiesTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginatorRequest
 *
 * @package Layer\Data\Paginator
 */
class PaginatorRequest implements PaginatorRequestInterface {

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	protected $request;

	/**
	 * @var string
	 */
	protected $pageKey = 'page';

	/**
	 * @var string
	 */
	protected $limitKey = 'limit';

	/**
	 * @var string
	 */
	protected $sortKeyKey = 'sort';

	/**
	 * @var string
	 */
	protected $directionKey = 'direction';

	/**
	 * @var string
	 */
	protected $ascValue = 'asc';

	/**
	 * @var string
	 */
	protected $descValue = 'desc';

	/**
	 * @param Request $request
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}

	/**
	 * @return int
	 */
	public function getCurrentPage() {

		return (int)$this->request->get($this->pageKey) ? : 1;
	}

	/**
	 * @return int|null
	 */
	public function getPerPage() {

		return ((int)$this->request->get($this->limitKey)) ? : null;
	}

	/**
	 * @return string|null
	 */
	public function getSortKey(array $whitelist = null) {

		$key = (string)$this->request->get($this->sortKeyKey);
		if ($key !== '' && ($whitelist === null || in_array($key, $whitelist, true))) {
			return $key;
		}
	}

	/**
	 * @return string|null
	 */
	public function getDirection() {

		$direction = strtolower($this->request->get($this->directionKey));
		if ($direction === $this->ascValue) {
			return 'asc';
		}
		if ($direction === $this->descValue) {
			return 'desc';
		}
	}

}