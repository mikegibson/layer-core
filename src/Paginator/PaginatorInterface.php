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
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null);

    /**
     * @return int
     */
    public function getCount();

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