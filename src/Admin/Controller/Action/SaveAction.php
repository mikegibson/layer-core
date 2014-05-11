<?php

namespace Layer\Admin\Controller\Action;

use Layer\Admin\Data\AdminFormType;
use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Data\DataType;
use Layer\Data\SingleRecordTrait;
use Symfony\Component\HttpFoundation\Request;

abstract class SaveAction implements ActionInterface {

	public function invoke(Application $app, Request $request) {

		$dataType = $request->get('dataType');

		$formData = $this->_getFormData($dataType, $request);

		$formBuilder = $app->form(new AdminFormType($dataType, 'edit'), $formData, [
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
				$app->addFlash('message', sprintf('The %s was saved', $dataType->singularHumanName));
				if($form->get('save_and_add')->isClicked()) {
					$redirect = $app['admin.helper']->url($dataType, 'add');
				} else {
					$redirect = $app['admin.helper']->url($dataType, 'edit', ['id' => $record->getId()]);
				}
				return $app->redirect($redirect);
			} else {
				$app->addFlash('error',
					sprintf('The %s could not be saved, please check for errors', $dataType->singularHumanName)
				);
			}
		}

		$data = [
			'dataType' => $dataType,
			'form' => $form->createView()
		];

		return $data;

	}

	abstract protected function _getFormData(DataType $dataType, Request $request);

}
