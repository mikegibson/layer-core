<?php

namespace Layer\Cms\Action;

use Symfony\Component\HttpFoundation\Request;

class AddAction extends SaveAction {

	public function getName() {
		return 'add';
	}

	public function getTemplate() {
		return '@cms/view/add';
	}

	public function getLabel() {
		return sprintf('Add new %s', $this->getRepository()->queryMetadata('getEntityHumanName'));
	}

	public function isVisible() {
		return true;
	}

	public function isCreate() {
		return true;
	}

	protected function getFormData(Request $request) {
		$formData = new \stdClass();
		$formData->record = $this->getRepository()->createEntity();
		return $formData;
	}

}