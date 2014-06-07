<?php

namespace Layer\Node;

use Layer\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerNode extends OrphanControllerNode implements ControllerNodeInterface, ActionInterface {

	private $routeName;

	/**
	 * @var ActionInterface
	 */
	private $action;

	private $name;

	public function __construct($routeName, ActionInterface $action, $name = null) {
		$this->routeName = $routeName;
		$this->action = $action;
		$this->name = $name;
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

	public function getName() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->getActionName();
	}

	public function getLabel() {
		return $this->getActionLabel();
	}

	public function getTemplate() {
		return $this->getAction()->getTemplate();
	}

	public function invoke(Request $request) {
		$ret = $this->getAction()->invoke($request);
		if(is_array($ret)) {
			$ret['node'] = $this;
		}
		return $ret;
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