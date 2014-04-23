<?php

namespace Layer\Controller;

use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ScaffoldController extends Controller {

	protected $actions = [];

	public function addAction(ActionInterface $action) {
		$this->actions[$action->getName()] = $action;
	}

	public function getCallable(Request $request) {
		$action = $request->get('action');
		if(!isset($this->actions[$action])) {
			return false;
		}
		return [$this->actions[$action], 'invoke'];
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getTemplate($name) {

		if(!isset($this->actions[$name])) {
			return false;
		}

		$action = $this->actions[$name];

		return $action->getTemplate();
	}

}