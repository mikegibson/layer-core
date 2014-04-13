<?php

namespace Layer\Data;

use Layer\Application;
use Layer\Data\Field\Field;
use League\Fractal\TransformerAbstract;

/**
 * Class Transformer
 * @package Layer\Data
 */
class Transformer extends TransformerAbstract {

    /**
     * @var \Layer\Application
     */
    protected $app;

    /**
     * @var DataType
     */
    protected $dataType;

    /**
     * @var bool|int
     */
    public $truncate = false;

    /**
     * @var bool
     */
    public $stripTags = false;

    /**
     * @param Application $app
     * @param DataType $dataType
     */
    public function __construct(Application $app, DataType $dataType) {
        $this->app = $app;
        $this->dataType = $dataType;
    }

    /**
     * @param array $data
     * @return array
     */
    public function transform(array $data) {

        foreach($data as $columnKey => $value) {
            if($field = $this->dataType->field($columnKey)) {
                $data[$columnKey] = $this->_processField($field, $data[$columnKey]);
            } else {
                $data[$columnKey] = $this->app->escape($data[$columnKey]);
            }
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
            if($this->stripTags) {
                $value = strip_tags($value);
            }
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