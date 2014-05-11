<?php

namespace Layer\Admin\Controller\Action;

use Layer\Data\DataType;
use Layer\Data\SingleRecordTrait;
use Symfony\Component\HttpFoundation\Request;

class EditAction extends SaveAction {

	public function getTemplate() {
		return '@admin/cms/edit.twig';
	}

	public function getName() {
		return 'edit';
	}

	protected function _getFormData(DataType $dataType, Request $request) {
		$formData = new \stdClass();
		$formData->record = $dataType->find($request->get('id'));
		return $formData;
	}

}