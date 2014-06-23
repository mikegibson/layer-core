<?php

namespace Layer\Cms\Action;

use Layer\Cms\Data\CmsRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AddActionFactory implements RepositoryCmsActionFactoryInterface {

	private $formFactory;

	private $urlGenerator;

	public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator) {
		$this->formFactory = $formFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function isRepositoryEligible(CmsRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		return !empty($crud->create);
	}

	public function createAction(CmsRepositoryInterface $repository) {
		return new AddAction($repository, $this->formFactory, $this->urlGenerator);
	}

}