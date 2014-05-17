<?php

namespace Layer\Admin\Controller\Action;

use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class AddAction extends SaveAction {

	public function getName() {
		return 'add';
	}

	public function getTemplate() {
		return '@admin/cms/add.twig';
	}

	protected function _getFormData(ManagedRepositoryInterface $repository, Request $request) {
		$formData = new \stdClass();
		$formData->record = $repository->createEntity();
		return $formData;
	}

}