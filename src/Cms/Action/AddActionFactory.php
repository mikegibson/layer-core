<?php

namespace Sentient\Cms\Action;

use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AddActionFactory implements RepositoryCmsActionFactoryInterface {

	private $formFactory;

	private $urlGenerator;

	public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator) {
		$this->formFactory = $formFactory;
		$this->urlGenerator = $urlGenerator;
	}

	public function isRepositoryEligible(ManagedRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		return !empty($crud->create);
	}

	public function createAction(ManagedRepositoryInterface $repository) {
		return new AddAction($repository, $this->formFactory, $this->urlGenerator);
	}

}