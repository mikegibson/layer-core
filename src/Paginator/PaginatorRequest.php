<?php

namespace Layer\Paginator;

use Layer\Utility\SetPropertiesTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PaginatorRequest
 *
 * @package Layer\Paginator
 */
class PaginatorRequest implements PaginatorRequestInterface {

    use SetPropertiesTrait;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var string
     */
    public $pageKey = 'page';

    /**
     * @var string
     */
    public $limitKey = 'limit';

    /**
     * @var string
     */
    public $sortKeyKey = 'sort';

    /**
     * @var string
     */
    public $directionKey = 'direction';

    /**
     * @var string
     */
    public $ascValue = 'asc';

    /**
     * @var string
     */
    public $descValue = 'desc';

    /**
     * @param Request $request
     */
    public function __construct(Request $request, array $config = []) {

        $this->_setProperties($config);
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getPage() {

        return (int)$this->request->get($this->pageKey) ? : 1;
    }

    /**
     * @return int|null
     */
    public function getLimit() {

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