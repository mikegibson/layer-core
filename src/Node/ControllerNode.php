<?php

namespace Layer\Node;

use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerNode extends OrphanControllerNode implements ControllerNodeInterface {

	private $routeName;

	/**
	 * @var ActionInterface
	 */
	private $action;

	private $key;

	public function __construct($routeName, ActionInterface $action, $key = null) {
		$this->routeName = $routeName;
		$this->action = $action;
		$this->key = $key;
	}

	public function getRouteName() {
		return $this->routeName;
	}

	public function getActionName() {
		return $this->getAction()->getName();
	}

	public function getActionLabel() {
		return $this->getAction()->getLabel();
	}

	public function getKey() {
		if($this->key !== null) {
			return $this->key;
		}
		return $this->getActionName();
	}

	public function getLabel() {
		return $this->getActionLabel();
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