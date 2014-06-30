<?php

namespace Sentient\Cms\Action;

use Sentient\Cms\Data\CmsRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EditActionFactory implements RepositoryCmsActionFactoryInterface {

	private $formFactory;

	private $urlGenerator;

	public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator) {
		$this->formFactory = $formFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function isRepositoryEligible(CmsRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		return !empty($crud->update);
	}

	public function createAction(CmsRepositoryInterface $repository) {
		return new EditAction($repository, $this->formFactory, $this->urlGenerator);
	}

}