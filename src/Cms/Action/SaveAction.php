<?php

namespace Layer\Cms\Action;

use Layer\Action\ActionInterface;
use Layer\Cms\Data\CmsRecordFormType;
use Layer\Cms\Data\CmsRepositoryInterface;
use Layer\Cms\Data\EntityFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class SaveAction implements ActionInterface {

	/**
	 * @var \Layer\Cms\Data\CmsRepositoryInterface
	 */
	private $repository;

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	private $formFactory;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @param CmsRepositoryInterface $repository
	 * @param FormFactoryInterface $formFactory
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(
		CmsRepositoryInterface $repository,
		FormFactoryInterface $formFactory,
		UrlGeneratorInterface $urlGenerator
	) {
		$this->repository = $repository;
		$this->formFactory = $formFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function invoke(Request $request) {

		$form = $this->getForm($request);
		$repository = $this->getRepository();

		if ($form->isSubmitted()) {
			$singularName = $repository->queryMetadata('getEntityHumanName');
			$flashBag = $request->getSession()->getFlashBag();
			if($form->isValid()) {
				$postData = $form->getData();
				$record = $postData->record;
				$entityManager = $this->getEntityManager();
				$entityManager->persist($record);
				$entityManager->flush();
				$message = sprintf('The %s was saved', $singularName);
				$flashBag->add('message', $message);
				if($repository->hasCmsNode('add') && $form->has('save_and_add') && $form->get('save_and_add')->isClicked()) {
					$nodePath = $repository->getCmsNode('add')->getPath();
					$redirect = $this->getUrlGenerator()->generate('cms', ['node' => $nodePath]);
				} elseif($repository->hasCmsNode('edit')) {
					$nodePath = $repository->getCmsNode('edit')->getPath();
					$redirect = $this->getUrlGenerator()->generate('cms', ['node' => $nodePath, 'id' => $record->getId()]);
				}
				return new RedirectResponse($redirect);
			} else {
				$flashBag->add('error', sprintf('The %s could not be saved, please check for errors', $singularName));
			}
		}

		$data = [
			'repository' => $repository,
			'form' => $form->createView()
		];

		return $data;

	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\Form\Form
	 */
	protected function getForm(Request $request) {
		$formData = $this->getFormData($request);
		$entityForm = new EntityFormType($this->getRepository(), $this->isCreate());
		$cmsForm = new CmsRecordFormType('edit', $entityForm);
		$name = $cmsForm->getName();
		$options = [
			'action' => $request->getRequestUri()
		];
		$formBuilder = $this->getFormFactory()->createNamedBuilder($name, $cmsForm, $formData, $options);
		$form = $formBuilder->getForm();
		$form->handleRequest($request);
		return $form;
	}

	/**
	 * @return CmsRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
	}

	protected function getEntityManager() {
		return $this->getRepository()->getEntityManager();
	}

	/**
	 * @return FormFactoryInterface
	 */
	protected function getFormFactory() {
		return $this->formFactory;
	}

	/**
	 * @return UrlGeneratorInterface
	 */
	protected function getUrlGenerator() {
		return $this->urlGenerator;
	}

	/**
	 * @return bool
	 */
	abstract protected function isCreate();

	/**
	 * @param Request $request
	 * @return mixed
	 */
	abstract protected function getFormData(Request $request);

}
