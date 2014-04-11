<?php

namespace Layer\Admin\Paginator;

use Layer\Data\Field\Field;
use Layer\Paginator\Paginator;

/**
 * Class CmsPaginator
 *
 * @package Layer\Admin\Paginator
 */
class CmsPaginator extends Paginator {

    /**
     * @var int
     */
    protected $truncate = 100;

    /**
     * @param int $page
     * @param null $limit
     * @param null $sortKey
     * @param null $direction
     * @return mixed
     */
    public function getData($page = 1, $limit = null, $sortKey = null, $direction = null) {

        $data = parent::getData($page, $limit, $sortKey, $direction);

        $columns = $this->getColumns();

        foreach($data as $rowKey => $row) {
            foreach($columns as $columnKey => $label) {
                if(isset($row[$columnKey])) {
                    if($field = $this->dataType->field($columnKey)) {
                        $row[$columnKey] = $this->_processField($field, $row[$columnKey]);
                    } else {
                        $row[$columnKey] = $this->app->escape($row[$columnKey]);
                    }
                }
            }
            $data[$rowKey] = $row;
        }

        return $data;

    }

    /**
     * @param Field $field
     * @param $value
     * @return string
     */
    protected function _processField(Field $field, $value) {
        if($field->htmlContent) {
            $value = strip_tags($value);
            $escape = false;
        } else {
            $escape = true;
        }
        if($this->truncate) {
            $value = $this->app['string_helper']->truncate($value, $this->truncate, ['html' => $field->htmlSafe]);
        }
        if($escape) {
            $value = $this->app->escape($value);
        }
        return $value;
    }

}