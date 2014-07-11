<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EditAction extends SaveAction {

	public function getTemplate(ManagedRepositoryInterface $repository) {
		return '@cms/view/edit';
	}

	public function getName() {
		return 'edit';
	}

	public function getLabel(ManagedRepositoryInterface $repository) {
		return sprintf('Edit %s', $repository->queryMetadata('getEntityHumanName'));
	}

	protected function isCreate() {
		return false;
	}

	protected function getFormData(ManagedRepositoryInterface $repository, Request $request) {
		if(!$id = $request->get('id')) {
			throw new HttpException(404, 'No ID was specified.');
		}
		if(!$entity = $repository->find($id)) {
			$humanName = $repository->queryMetadata('getEntityHumanName');
			$message = sprintf('No %s exists with ID %d.', $humanName, $id);
			throw new HttpException(404, $message);
		}
		$formData = new \stdClass();
		$formData->entity = $entity;
		return $formData;
	}

}