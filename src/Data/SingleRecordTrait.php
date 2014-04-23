<?php

namespace Layer\Data;

use Layer\Application;
use Symfony\Component\HttpFoundation\Request;

trait SingleRecordTrait {

	/**
	 * @param DataType $dataType
	 * @param Request $request
	 * @param Model $model
	 * @param null $requestParam
	 * @param null $keyField
	 * @param int $abort
	 * @return bool
	 */
	protected function _getSingleRecord(
		Application $app,
		DataType $dataType,
		Request $request,
		Model $model = null,
		$requestParam = null,
		$keyField = null,
		$abort = 404
	) {

		if ($model === null) {
			$model = $dataType->model();
		}

		if ($keyField === null) {
			$keyField = $model->getKeyName();
		}

		if ($requestParam === null) {
			$requestParam = $keyField;
		}

		if (!$value = $request->get($requestParam)) {
			if ($abort) {
				$app->abort($abort);
			}
			return false;
		}

		return $model->where($keyField, $value)->firstOrFail();

	}

}