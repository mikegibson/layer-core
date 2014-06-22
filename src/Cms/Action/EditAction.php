<?php

namespace Layer\Cms\Action;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EditAction extends SaveAction {

	public function getTemplate() {
		return '@cms/view/edit';
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

	protected function isCreate() {
		return false;
	}

	protected function getFormData(Request $request) {
		if(!$id = $request->get('id')) {
			throw new HttpException(404, 'No ID was specified.');
		}
		if(!$entity = $this->getRepository()->find($id)) {
			$humanName = $this->getRepository()->queryMetadata('getEntityHumanName');
			$message = sprintf('No %s exists with ID %d.', $humanName, $id);
			throw new HttpException(404, $message);
		}
		$formData = new \stdClass();
		$formData->entity = $entity;
		return $formData;
	}

}