<?php

namespace Layer\Node;

use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerNode extends OrphanControllerNode implements ControllerNodeInterface {

	/**
	 * @var ActionInterface
	 */
	private $action;

	private $name;

	public function __construct(ActionInterface $action, $name = null) {
		$this->action = $action;
		$this->name = $name;
	}

	public function getActionName() {
		$action = $this->getAction();
		if(!is_object($action)) {
			var_dump(\get_call_stack());
			var_dump($action);
			die('here');
		}
		return $action->getName();
	}

	public function getName() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->getActionName();
	}

	public function getLabel() {
		return $this->getAction()->getLabel();
	}

	public function getTemplate() {
		return $this->getAction()->getTemplate();
	}

	public function invokeAction(Request $request) {
		return $this->getAction()->invoke($request);
	}

	public function isVisible() {
		return $this->getAction()->isVisible();
	}

	public function getVisibleChildNodes() {
		$nodes = [];
		foreach($this->getChildNodes() as $node) {
			if($node->isVisible()) {
				$nodes[] = $node;
			}
		}
		return $nodes;
	}

	protected function getAction() {
		return $this->action;
	}

}