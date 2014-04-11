<?php

namespace Layer\Paginator;

use Layer\View\Table\TableDataInterface;

/**
 * Interface PaginatorQueryInterface
 *
 * @package Layer\Paginator
 */
interface PaginatorResultInterface extends TableDataInterface {

    /**
     * @return array
     */
    public function getColumns();

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @return mixed
     */
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null);

    /**
     * @return int
     */
    public function getCount();

}