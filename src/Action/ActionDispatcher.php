<?php

namespace Layer\Action;

use Layer\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ActionDispatcher
 * @package Layer\Action
 */
class ActionDispatcher {

	/**
	 * @var \Layer\View\ViewInterface
	 */
	protected $view;

	/**
	 * @param ViewInterface $view
	 */
	public function __construct(ViewInterface $view) {
		$this->view = $view;
	}

	/**
	 * @param ActionInterface $action
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
		return $this->view->render($template, $data);
	}

}