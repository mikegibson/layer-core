<?php

namespace Layer\Data;

use Symfony\Component\HttpFoundation\Request;

trait SingleRecordTrait {

    protected $requestParam = 'id';

    protected function _getSingleRecord(
        DataType $dataType,
        Request $request,
        Model $model = null,
        $abort = 404
    ) {

        if(!$value = $request->get($this->requestParam)) {
            if($abort) {
                $this->app->abort($abort);
            }
            return false;
        }

        if($model === null) {
            $model = $dataType->model();
        }

        return $model->where('id', $value)->first();

    }

}