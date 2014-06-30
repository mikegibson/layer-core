<?php

namespace Sentient\Node;

use Sentient\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerNode extends Node implements ControllerNodeInterface, ActionInterface {

	private $routeName;

	/**
	 * @var ActionInterface
	 */
	private $action;

	private $template;

	private $visible;

	private $accessible;

	public function __construct(
		$routeName,
		ActionInterface $action = null,
		ControllerNodeInterface $parentNode = null,
		$name = null,
		$label = null,
		$template = null,
		$visible = null,
		$accessible = null
	) {
		parent::__construct($parentNode);
		$this->routeName = $routeName;
		$this->action = $action;
		$this->name = $name;
		$this->label = $label;
		$this->template = $template;
		$this->visible = $visible;
		$this->accessible = $accessible;
	}

	public function getRouteName() {
		return $this->routeName;
	}

	public function getActionName() {
		if($action = $this->getAction()) {
			return $action->getName();
		}
	}

	public function getActionLabel() {
		if($action = $this->getAction()) {
			return $action->getLabel();
		}
	}

	public function getName() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->getActionName();
	}

	public function getLabel() {
		if($this->label !== null) {
			return $this->label;
		}
		return $this->getActionLabel();
	}

	public function getTemplate() {
		if($this->template !== null) {
			return $this->template;
		}
		if($action = $this->getAction()) {
			return $action->getTemplate();
		}
	}

	public function invoke(Request $request) {
		if(!$this->isAccessible()) {
			throw new \BadMethodCallException('This node is not accessible.');
		}
		$ret = null;
		if($action = $this->getAction()) {
			$ret = $action->invoke($request);
		}
		if(is_array($ret)) {
			$ret['node'] = $this;
		}
		return $ret;
	}

	public function isAccessible() {
		if($this->accessible !== null) {
			return !!$this->accessible;
		}
		return !!$this->getAction();
	}

	public function isDirectlyAccessible() {
		if($action = $this->getAction()) {
			return $action->isDirectlyAccessible();
		}
		return $this->isAccessible();
	}

	public function isVisible() {
		if($this->visible !== null) {
			return !!$this->visible;
		}
		return $this->isDirectlyAccessible() && $this->getAction()->isVisible();
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

	public function registerChildNode(NodeInterface $childNode) {
		if(!$childNode instanceof ControllerNodeInterface) {
			throw new \InvalidArgumentException('Child nodes must implement ControllerNodeInterface.');
		}
		parent::registerChildNode($childNode);
	}

	protected function getAction() {
		return $this->action;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedControllerNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

}