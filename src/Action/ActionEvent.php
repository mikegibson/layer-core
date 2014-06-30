<?php

namespace Sentient\Action;

use Sentient\View\ViewInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class ActionEvent extends Event {

	const BEFORE_DISPATCH = 'action.beforeDispatch';

	const BEFORE_RENDER = 'action.beforeRender';

	const AFTER_DISPATCH = 'action.afterDispatch';

	/**
	 * @var ActionInterface
	 */
	private $action;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @var mixed $response
	 */
	private $result;

	/**
	 * @var string $template
	 */
	private $template;

	/**
	 * @var ViewInterface
	 */
	private $view;

	/**
	 * @var mixed $response
	 */
	private $response;

	/**
	 * @param ActionInterface $action
	 * @param Request $request
	 * @param ViewInterface $view
	 */
	public function __construct(ActionInterface $action, Request $request, ViewInterface $view) {
		$this->action = $action;
		$this->request = $request;
		$this->setView($view);
		$this->setTemplate($action->getTemplate());
	}

	/**
	 * @return ActionInterface
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * @return mixed
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @return ViewInterface
	 */
	public function getView() {
		return $this->view;
	}

	/**
	 * @return mixed
	 */
	public function getResponse() {
		return $this->response;
	}

	/**
	 * @param mixed $result
	 */
	public function setResult($result) {
		$this->result = $result;
	}

	/**
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * @param ViewInterface $view
	 */
	public function setView(ViewInterface $view) {
		$this->view = $view;
	}

	/**
	 * @param mixed $response
	 */
	public function setResponse($response) {
		$this->response = $response;
	}

}