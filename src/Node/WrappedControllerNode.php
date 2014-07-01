<?php

namespace Sentient\Node;

use Sentient\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class WrappedControllerNode extends WrappedNode implements ControllerNodeInterface, ActionInterface {

	public function getActionName() {
		return $this->getBaseNode()->getActionName();
	}

	public function getActionLabel() {
		return $this->getBaseNode()->getActionLabel();
	}

	public function isVisible() {
		return $this->getBaseNode()->isVisible();
	}

	public function isAccessible() {
		return $this->getBaseNode()->isAccessible();
	}

	public function isDirectlyAccessible() {
		return $this->getBaseNode()->isDirectlyAccessible();
	}

	public function getVisibleChildNodes() {
		$nodes = [];
		foreach($this->getChildNodes() as $name => $node) {
			if($node->isVisible()) {
				$nodes[$name] = $node;
			}
		}
		return $nodes;
	}

	public function getTemplate() {
		return $this->getBaseNode()->getTemplate();
	}

	public function invoke(Request $request) {
		return $this->getBaseNode()->invoke($request);
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedControllerNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

	/**
	 * @return ControllerNodeInterface
	 */
	public function getBaseNode() {
		return parent::getBaseNode();
	}

}