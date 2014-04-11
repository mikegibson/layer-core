<?php

namespace Layer\Paginator;

use Layer\Data\DataType;
use Layer\Data\QueryBuilder;
use Layer\Utility\SetPropertiesTrait;

/**
 * Class PaginatorQuery
 *
 * @package Layer\Paginator
 */
class PaginatorResult implements PaginatorResultInterface {

    use SetPropertiesTrait;

    /**
     * @var \Layer\Data\DataType
     */
    protected $dataType;

    /**
     * @var \Layer\Data\QueryBuilder
     */
    protected $builder;

    /**
     * @var int
     */
    public $limit = 10;

    /**
     * @var int
     */
    public $maxLimit = 100;

    /**
     * @param DataType $dataType
     * @param QueryBuilder $builder
     */
    public function __construct(DataType $dataType, QueryBuilder $builder = null, array $config = []) {

        $this->_setProperties($config);
        $this->dataType = $dataType;
        if ($builder !== null) {
            $this->setQuery($builder);
        }
    }

    /**
     * @param QueryBuilder $builder
     */
    public function setQuery(QueryBuilder $builder) {

        $this->query = $builder;
    }

    /**
     * @return QueryBuilder
     */
    public function getQuery() {

        return $this->query;
    }

    /**
     * @return array
     */
    public function getColumns() {

        $columns = [];
        foreach ($this->dataType->fields() as $name => $field) {
            if (!$field->visible || !$field->important) {
                continue;
            }
            $columns[$name] = $field->label;
        }

        return $columns;
    }

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null, $columns = ['*'], QueryBuilder $builder = null) {

        return $this->paginateQuery($page, $limit, $sortKey, $direction, $builder)->get($columns);
    }

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    public function paginateQuery($page = 1, $limit = null, $sortKey = null, $direction = null, QueryBuilder $builder = null) {

        $builder = $this->_getQuery($builder);

        $limit = (int)$limit;
        if ($limit < 1) {
            $limit = $this->limit;
        }

        $limit = min([$limit, $this->maxLimit]);

        return $builder->forPage(1, $limit);
    }


    /**
     * @return int
     */
    public function getCount(QueryBuilder $builder = null) {

        $builder = $this->_getQuery($builder);

        return $builder->count();
    }

    /**
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \Exception
     */
    protected function _getQuery(QueryBuilder $builder = null) {

        if ($builder === null) {
            if ($this->query === null) {
                throw new \Exception('No query object specified!');
            }
            $builder = $this->query;
        }

        return $builder;
    }

}