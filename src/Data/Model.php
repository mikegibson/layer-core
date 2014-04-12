<?php

namespace Layer\Data;

use Layer\Application;

class Model extends \Illuminate\Database\Eloquent\Model {

    protected $app;

    protected $dataType;

    public $timestamps = false;

    public function __construct(Application $app, DataType $dataType, array $attributes = []) {

        parent::__construct();

        $this->app = $app;
        $this->dataType = $dataType;

        $this->fillable(array_keys($dataType->fields()));
        $this->setTable($dataType->table);
        $this->setConnectionResolver($this->app['db']->getDatabaseManager());
        $this->setRawAttributes($this->fillableFromArray($attributes));

    }

    public function setAttributes(array $attributes) {

        foreach($this->fillableFromArray($attributes) as $k => $v) {
            $this->setAttribute($k, $v);
        }

    }

    public function newInstance($attributes = array(), $exists = false) {

        $model = new static($this->app, $this->dataType, (array) $attributes);

        $model->exists = $exists;

        return $model;
    }

}