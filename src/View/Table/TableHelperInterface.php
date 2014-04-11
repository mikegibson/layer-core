<?php

namespace Layer\View\Table;

/**
 * Interface TableHelperInterface
 *
 * @package Layer\View\Table
 */
interface TableHelperInterface {

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function render(TableDataInterface $data);

    /**
     * @param $inner
     * @return mixed
     */
    public function wrap($inner);

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function thead(TableDataInterface $data);

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function headerRow(TableDataInterface $data);

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function headerColumns(TableDataInterface $data);

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function tbody(TableDataInterface $data);

    /**
     * @param TableDataInterface $data
     * @return mixed
     */
    public function bodyRows(TableDataInterface $data);

    /**
     * @param $row
     * @return mixed
     */
    public function row($row);

    /**
     * @param $label
     * @return mixed
     */
    public function headerCell($label);

    /**
     * @param $value
     * @return mixed
     */
    public function cell($value);

}