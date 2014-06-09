<?php

namespace Layer\Users\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class LoginFormType extends AbstractType {

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @param Request $request A request instance
	 */
	public function __construct(Request $request) {
		$this->request = $request;
	}

	public function getName() {
		return 'login';
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('username', 'text', [
				'required' => true
			])
			->add('password', 'password', [
				'required' => true
			]);

		if(!isset($options['remember_me']) || $options['remember_me']) {
			$builder->add('remember_me', 'checkbox', [
				'label' => 'Remember me?',
				'required' => false
			]);
		}

		$builder->add('login', 'submit');

		$request = $this->getRequest();

		/* Note: since the Security component's form login listener intercepts
		 * the POST request, this form will never really be bound to the
		 * request; however, we can match the expected behavior by checking the
		 * session for an authentication error and last username.
		 */
		$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($request) {

			if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
				$error = $request->attributes->get(SecurityContextInterface::AUTHENTICATION_ERROR);
			} else {
				$session = $request->getSession();
				if($session !== null && $session->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
					$error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
					$session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
				} else {
					$error = null;
				}
			}

			if ($error) {
				$event->getForm()->addError(new FormError($error->getMessage()));
			}

			$event->setData(array_replace((array) $event->getData(), [
				'username' => $request->getSession()->get(SecurityContextInterface::LAST_USERNAME),
			]));
		});

	}

	/**
	 * @return Request
	 */
	protected function getRequest() {
		return $this->request;
	}

}