<?php

namespace Layer\Admin\Controller\Action;

use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class AddAction implements ActionInterface {

	public function getName() {
		return 'add';
	}

	public function getTemplate() {
		return '@admin/cms/add.twig';
	}

	public function invoke(Application $app, Request $request) {

		$dataType = $request->get('dataType');
		$formBuilder = $dataType->getFormBuilder('admin_add');
		$formBuilder->setAction($request->getRequestUri());

		$form = $formBuilder->getForm();

		$form->handleRequest($request);
		if($form->isSubmitted()) {
			if($form->isValid()) {
				$model = $dataType->model();
				$model->setAttributes($form->getData());
				if(!$model->save()) {
					throw new \Exception(sprintf('The %s could not be saved!', $dataType->singularHumanName));
				}
				$app->addFlash('message', sprintf('The %s was added', $dataType->singularHumanName));
				return $app->redirect($app['admin.helper']->url($dataType, 'edit', ['id' => $model->id]));
			} else {
				$app->addFlash('error',
					sprintf('The %s could not be saved, please check for errors', $dataType->singularHumanName)
				);
			}
		}

		$data = compact('dataType');
		$data['form'] = $form->createView();

		return $data;

	}

}