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

	public function __construct(
		$routeName,
		ActionInterface $action,
		ControllerNodeInterface $parentNode = null,
		$name = null
	) {
		$this->routeName = $routeName;
		$this->action = $action;
		$this->parentNode = $parentNode;
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