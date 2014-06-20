<?php

namespace Layer\Users\Action;

use Layer\Action\ActionInterface;
use Layer\Users\Form\LoginFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
	 * @var \Symfony\Component\Security\Core\SecurityContextInterface
	 */
	protected $securityContext;

	/**
	 * @param FormFactoryInterface $formFactory
	 * @param null $checkPath
	 * @param null $template
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
		if($this->securityContext === null) {
			return true;
		}
		return !$this->securityContext->getToken();
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setSecurityContext(SecurityContextInterface $securityContext) {
		$this->securityContext = $securityContext;
	}

}