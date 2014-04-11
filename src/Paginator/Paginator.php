<?php

namespace Layer\Paginator;

use Layer\Application;
use Layer\Data\DataType;

/**
 * Class Paginator
 *
 * @package Controller\Paginator
 */
class Paginator implements PaginatorInterface {

    /**
     * @var \Layer\Application
     */
    protected $app;

    /**
     * @var \Layer\Data\DataType
     */
    protected $dataType;

    /**
     * @var \Layer\Paginator\PaginatorRequestInterface
     */
    protected $request;

    /**
     * @var PaginatorResultInterface
     */
    protected $result;

    /**
     * Cached vars
     *
     * @var array
     */
    protected $_vars = [];

    /**
     * @param PaginatorRequestInterface $request
     * @param PaginatorResultInterface $result
     */
    public function __construct(
        Application $app,
        DataType $dataType,
        PaginatorRequestInterface $request,
        PaginatorResultInterface $result
    ) {
        $this->app = $app;
        $this->dataType = $dataType;
        $this->request = $request;
        $this->result = $result;
    }

    /**
     * @return array
     */
    public function getColumns() {

        return $this->result->getColumns();
    }

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     */
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null) {

        return $this->result->getData($page, $limit, $sortKey, $direction);
    }

    /**
     * @return int
     */
    public function getCount() {

        if (!isset($this->_vars['count'])) {
            $this->_vars['count'] = $this->result->getCount();
        }

        return $this->_vars['count'];
    }

    /**
     * @return int
     */
    public function getPage() {

        if (!isset($this->_vars['page'])) {
            $this->_vars['page'] = $this->request->getPage();
        }

        return $this->_vars['page'];
    }

    /**
     * @return int
     */
    public function getPageCount() {
        return (int) ceil($this->getCount() / $this->getLimit());
    }

    /**
     * @return bool
     */
    public function hasNext() {
        return ($this->getCount() > $this->getPage());
    }

    /**
     * @return bool
     */
    public function hasPrev() {
        return ($this->getPage() > 1);
    }

    /**
     * @param $page
     * @return bool
     */
    public function hasPage($page) {
        return ($this->getPageCount() >= (int) $page);
    }

    /**
     * @return int
     */
    public function getLimit() {

        if (!isset($this->_vars['limit'])) {
            $this->_vars['limit'] = $this->request->getLimit();
        }

        return $this->_vars['limit'];
    }

    /**
     * @return string|null
     */
    public function getSortKey() {

        if (!isset($this->_vars['sortKey'])) {
            $this->_vars['sortKey'] = $this->request->getSortKey();
        }

        return $this->_vars['sortKey'];
    }

    /**
     * @return string|null
     */
    public function getDirection() {

        if (!isset($this->_vars['direction'])) {
            $this->_vars['direction'] = $this->request->getDirection();
        }

        return $this->_vars['direction'];
    }

}