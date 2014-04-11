<?php

namespace Layer\Data;

use Symfony\Component\HttpFoundation\Request;

trait SingleRecordTrait {

    protected $requestParam = 'id';

    protected function _getSingleRecord(
        DataType $dataType,
        Request $request,
        QueryBuilder $query = null,
        $abort = 404
    ) {

        if(!$value = $request->get($this->requestParam)) {
            if($abort) {
                $this->app->abort($abort);
            }
            return false;
        }

        if($query === null) {
            $query = $dataType->query();
        }

        return $query->where('id', $value)->first();

    }

}