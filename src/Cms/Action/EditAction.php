<?php

namespace Layer\Cms\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EditAction extends SaveAction {

	public function getTemplate() {
		return '@cms/view/edit.twig';
	}

	public function getName() {
		return 'edit';
	}

	public function getLabel() {
		return sprintf('Edit %s', $this->getRepository()->queryMetadata('getEntityHumanName'));
	}

	public function isVisible() {
		return false;
	}

	public function isCreate() {
		return false;
	}

	protected function getFormData(Request $request) {
		if(!$id = $request->get('id')) {
			throw new HttpException(404, 'No ID was specified.');
		}
		if(!$record = $this->getRepository()->find($id)) {
			throw new HttpException(404, sprintf('No record exists with ID %d.', $id));
		}
		$formData = new \stdClass();
		$formData->record = $record;
		return $formData;
	}

}