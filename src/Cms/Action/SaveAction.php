<?php

namespace Sentient\Cms\Action;

use Sentient\Action\ActionInterface;
use Sentient\Cms\Data\CmsEntityFormType;
use Sentient\Cms\Data\EntityFormType;
use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class SaveAction implements ActionInterface {

	/**
	 * @var ManagedRepositoryInterface
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
	 * @param ManagedRepositoryInterface $repository
	 * @param FormFactoryInterface $formFactory
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(
		ManagedRepositoryInterface $repository,
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
				$entity = $postData->entity;
				$this->getRepository()->save($entity);
				$message = sprintf('The %s was saved', $singularName);
				$flashBag->add('message', $message);
				if(
					$repository->queryMetadata('hasCmsNode', ['action' => 'add']) &&
					$form->has('save_and_add') &&
					$form->get('save_and_add')->isClicked()
				) {
					$nodePath = $repository->queryMetadata('getCmsNode', ['action' => 'add'])->getPath();
					$redirect = $this->getUrlGenerator()->generate('cms', ['node' => $nodePath]);
				} elseif($repository->queryMetadata('hasCmsNode', ['action' => 'edit'])) {
					$nodePath = $repository->queryMetadata('getCmsNode', ['action' => 'edit'])->getPath();
					$redirect = $this->getUrlGenerator()->generate('cms', ['node' => $nodePath, 'id' => $entity->getId()]);
				}
				return new RedirectResponse($redirect);
			} else {
				$flashBag->add('error', sprintf('The %s could not be saved, please check for errors', $singularName));
			}
		}

		$data = [
			'repository' => $repository,
			'entity' => $form->getData()->entity,
			'form' => $form->createView()
		];

		return $data;

	}

	public function isVisible() {
		return $this->isCreate();
	}

	public function isDirectlyAccessible() {
		return $this->isCreate();
	}

	/**
	 * @param Request $request
	 * @return \Symfony\Component\Form\Form
	 */
	protected function getForm(Request $request) {
		$formData = $this->getFormData($request);
		$entityForm = new EntityFormType($this->getRepository(), $this->isCreate());
		$cmsForm = new CmsEntityFormType('edit', $entityForm);
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
	 * @return ManagedRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
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
