<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class AddAction extends SaveAction {

	public function getName() {
		return 'add';
	}

	public function getTemplate(ManagedRepositoryInterface $repository) {
		return '@cms/view/add';
	}

	public function getLabel(ManagedRepositoryInterface $repository) {
		return sprintf('Add new %s', $repository->queryMetadata('getEntityHumanName'));
	}

	protected function isCreate() {
		return true;
	}

	protected function getFormData(ManagedRepositoryInterface $repository, Request $request) {
		$formData = new \stdClass();
		$formData->entity = $repository->createEntity();
		return $formData;
	}

}