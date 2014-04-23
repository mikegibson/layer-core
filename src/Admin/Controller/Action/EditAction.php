<?php

namespace Layer\Admin\Controller\Action;

use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Data\SingleRecordTrait;
use Symfony\Component\HttpFoundation\Request;

class EditAction implements ActionInterface {

	use SingleRecordTrait;

	public function getName() {
		return 'edit';
	}

	public function getTemplate() {
		return '@admin/cms/edit.twig';
	}

	public function invoke(Application $app, Request $request) {

		$dataType = $request->get('dataType');
		$record = $this->_getSingleRecord($app, $dataType, $request);
		$array = $record->toArray();

		$formBuilder = $dataType->getFormBuilder('admin_edit', $array);
		$formBuilder->setAction($request->getRequestUri());

		$form = $formBuilder->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if($form->isValid()) {
				$record->setAttributes($form->getData());
				if(!$record->save()) {
					throw new \Exception(sprintf('The %s could not be saved!', $dataType->singularHumanName));
				}
				$app->addFlash('message', sprintf('The %s was saved', $dataType->singularHumanName));
				return $app->redirect($request->getRequestUri());
			} else {
				$app->addFlash('error',
					sprintf('The %s could not be saved, please check for errors', $dataType->singularHumanName)
				);
			}
		}

		$data = compact('dataType', 'record');
		$data['form'] = $form->createView();

		return $data;

	}

}