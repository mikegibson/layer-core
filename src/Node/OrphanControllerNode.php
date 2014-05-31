<?php

namespace Layer\Node;

abstract class OrphanControllerNode extends OrphanNode implements ControllerNodeInterface {

	public function registerChildNode(NodeInterface $childNode) {
		if(!$childNode instanceof ControllerNodeInterface) {
			throw new \InvalidArgumentException('Child nodes must implement ControllerNodeInterface.');
		}
		return parent::registerChildNode($childNode);
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null) {
		return new WrappedControllerNode($baseNode, $this, $name, $label);
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