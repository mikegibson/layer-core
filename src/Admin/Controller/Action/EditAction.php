<?php

namespace Layer\Admin\Controller\Action;

use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\SingleRecordTrait;
use Symfony\Component\HttpFoundation\Request;

class EditAction extends SaveAction {

	public function getTemplate() {
		return '@admin/cms/edit.twig';
	}

	public function getName() {
		return 'edit';
	}

	protected function _getFormData(ManagedRepositoryInterface $repository, Request $request) {
		$formData = new \stdClass();
		$formData->record = $repository->find($request->get('id'));
		return $formData;
	}

}