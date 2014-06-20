<?php

namespace Layer\Node;

use Layer\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class ControllerNode extends AbstractNode implements ControllerNodeInterface, ActionInterface {

	private $routeName;

	/**
	 * @var ActionInterface
	 */
	private $action;

	private $name;

	private $label;

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
		$this->routeName = $routeName;
		$this->action = $action;
		$this->parentNode = $parentNode;
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
		return $this->isAccessible() ? $this->getAction()->getName() : null;
	}

	public function getActionLabel() {
		return $this->isAccessible() ? $this->getAction()->getLabel() : null;
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
		return $this->isAccessible() ? $this->getAction()->getTemplate() : null;
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

	public function isVisible() {
		if($this->visible !== null) {
			return !!$this->visible;
		}
		return $this->isAccessible() && $this->getAction()->isVisible();
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