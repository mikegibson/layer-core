<?php

namespace Layer\Admin\Controller\Action;

use Layer\Admin\Data\AdminFormType;
use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class SaveAction implements ActionInterface {

	public function invoke(Application $app, Request $request) {

		$repository = $request->get('repository');
		
		if(!$repository instanceof ManagedRepositoryInterface) {
			return $app->abort(500);
		}

		$formData = $this->_getFormData($repository, $request);

		$formBuilder = $app->form(new AdminFormType($repository, 'edit'), $formData, [
			'action' => $request->getRequestUri()
		]);

		$form = $formBuilder->getForm();
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			if($form->isValid()) {
				$postData = $form->getData();
				$record = $postData->record;
				$app['orm.em']->persist($record);
				$app['orm.em']->flush();
				$app->addFlash('message', sprintf('The %s was saved', $repository->getSingularHumanName()));
				if($form->get('save_and_add')->isClicked()) {
					$redirect = $app['admin.helper']->url($repository, 'add');
				} else {
					$redirect = $app['admin.helper']->url($repository, 'edit', ['id' => $record->getId()]);
				}
				return $app->redirect($redirect);
			} else {
				$app->addFlash('error',
					sprintf('The %s could not be saved, please check for errors', $repository->getSingularHumanName())
				);
			}
		}

		$data = [
			'repository' => $repository,
			'form' => $form->createView()
		];

		return $data;

	}

	abstract protected function _getFormData(ManagedRepositoryInterface $repository, Request $request);

}
