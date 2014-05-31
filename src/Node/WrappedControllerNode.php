<?php

namespace Layer\Node;

use Symfony\Component\HttpFoundation\Request;

class WrappedControllerNode extends WrappedNode implements ControllerNodeInterface {

	public function __construct(
		ControllerNodeInterface $baseNode,
		ControllerNodeInterface $parentNode = null,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true
	) {
		parent::__construct($baseNode, $parentNode, $name, $label, $baseChildrenAccessible);
	}

	public function getRouteName() {
		return $this->getBaseNode()->getRouteName();
	}

	public function getActionName() {
		return $this->getBaseNode()->getActionName();
	}

	public function getActionLabel() {
		return $this->getBaseNode()->getActionLabel();
	}

	public function isVisible() {
		return $this->getBaseNode()->isVisible();
	}

	public function getVisibleChildNodes() {
		$nodes = [];
		foreach($this->getChildNodes() as $key => $node) {
			if($node->isVisible()) {
				$nodes[$key] = $node;
			}
		}
		return $nodes;
	}

	public function getTemplate() {
		return $this->getBaseNode()->getTemplate();
	}

	public function invokeAction(Request $request) {
		return $this->getBaseNode()->invokeAction($request);
	}

	protected function createWrappedNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedControllerNode($baseNode, $this, $key, $label, $baseChildrenAccessible);
	}

	/**
	 * @return ControllerNodeInterface
	 */
	public function getBaseNode() {
		return parent::getBaseNode();
	}

}