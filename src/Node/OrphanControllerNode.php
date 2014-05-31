<?php

namespace Layer\Node;

abstract class OrphanControllerNode extends OrphanNode implements ControllerNodeInterface {

	public function registerChildNode(NodeInterface $childNode) {
		if(!$childNode instanceof ControllerNodeInterface) {
			throw new \InvalidArgumentException('Child nodes must implement ControllerNodeInterface.');
		}
		return parent::registerChildNode($childNode);
	}

	protected function createWrappedNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedControllerNode($baseNode, $this, $key, $label, $baseChildrenAccessible);
	}

	public function isVisible() {
		return true;
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

}