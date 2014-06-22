<?php

namespace Layer\Node;

class ListNode extends Node implements ListNodeInterface {

	public function __construct(ListNodeInterface $parentNode = null, $name = null, $label = null) {
		parent::__construct($parentNode, $name, $label);
	}

	public function areChildrenOrdered() {
		return false;
	}

	public function getUrl(array $params = null) {
		return null;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedListNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

}