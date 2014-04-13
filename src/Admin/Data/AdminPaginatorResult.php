<?php

namespace Layer\Admin\Data;

use Illuminate\Database\Query\Builder;
use Layer\Paginator\PaginatorResult;
use League\Fractal\Resource\Collection;

/**
 * Class AdminPaginatorResult
 * @package Layer\Admin\Data
 */
class AdminPaginatorResult extends PaginatorResult {

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @param array $columns
     * @param Builder $builder
     * @return mixed
     */
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null, $columns = ['*'], Builder $builder = null) {

        $collection = $this->getCollection($page, $limit, $sortKey, $direction, $columns, $builder);

        $array = $this->app['fractal']->createData($collection)->toArray();

        return $array['data'];
    }

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @param array $columns
     * @param Builder $builder
     * @return Collection
     */
    public function getCollection($page = 1, $limit = null, $sortKey = null, $direction = null, $columns = ['*'], Builder $builder = null) {

        $result = parent::getData($page, $limit, $sortKey, $direction, $columns, $builder);

        return new Collection($result, $this->_getTransformer());
    }


    /**
     * @return Transformer
     */
    protected function _getTransformer() {
        return new AdminIndexTransformer($this->app, $this->dataType);
    }

}