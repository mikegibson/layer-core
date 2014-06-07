<?php

namespace Layer\Action;

use Layer\Node\ControllerNodeInterface;
use Layer\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActionDispatcher {

	/**
	 * @var \Layer\View\ViewInterface
	 */
	private $view;

	/**
	 * @param ViewInterface $view
	 */
	public function __construct(ViewInterface $view) {
		$this->view = $view;
	}

	/**
	 * @param ControllerNodeInterface $node
	 * @param Request $request
	 * @return array
	 */
	public function dispatch(ActionInterface $action, Request $request) {
		$template = $action->getTemplate();
		$data = $action->invoke($request) ?: [];
		if($data instanceof Response) {
			return $data;
		}
		$data['action'] = $action;
		return $this->getView()->render($template, $data);
	}

	protected function throwNotFoundException() {
		throw new HttpException(404, 'Page not found.');
	}

	/**
	 * @return ViewInterface
	 */
	protected function getView() {
		return $this->view;
	}

}