<?php

namespace Layer\Users\Action;

use Layer\Action\ActionInterface;
use Layer\Users\Form\LoginFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class LoginAction implements ActionInterface {

	/**
	 * @var \Symfony\Component\Form\FormFactoryInterface
	 */
	protected $formFactory;

	/**
	 * @var string
	 */
	protected $checkPath;

	/**
	 * @var string
	 */
	protected $template;

	/**
	 * @param FormFactoryInterface $formFactory
	 * @param null|string $checkPath
	 * @param null|string $template
	 */
	public function __construct(
		FormFactoryInterface $formFactory,
		$checkPath = null,
		$template = null
	) {
		$this->formFactory = $formFactory;
		if($checkPath !== null) {
			$this->checkPath = $checkPath;
		}
		if($template !== null) {
			$this->template = $template;
		}
	}

	public function getName() {
		return 'login';
	}

	public function getLabel() {
		return 'Login';
	}

	public function invoke(Request $request) {

		$type = new LoginFormType($request);

		$builder = $this->formFactory->createBuilder($type, [], ['action' => $this->checkPath]);

		$form = $builder->getForm();

		return [
			'form' => $form->createView()
		];
	}

	public function isVisible() {
		return true;
	}

	public function getTemplate() {
		return $this->template;
	}

}