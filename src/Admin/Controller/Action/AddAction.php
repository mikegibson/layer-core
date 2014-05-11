<?php

namespace Layer\Admin\Controller\Action;

use Layer\Data\DataType;
use Symfony\Component\HttpFoundation\Request;

class AddAction extends SaveAction {

	public function getName() {
		return 'add';
	}

	public function getTemplate() {
		return '@admin/cms/add.twig';
	}

	protected function _getFormData(DataType $dataType, Request $request) {
		$formData = new \stdClass();
		$formData->record = $dataType->createEntity();
		return $formData;
	}

}