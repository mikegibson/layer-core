<?php

namespace Layer\Node;

abstract class ListNode extends AbstractNode implements ListNodeInterface {

	public function areChildrenOrdered() {
		return false;
	}

	public function getUrl(array $params = null) {
		return null;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedListNode($baseNode, $this, $key, $label, $baseChildrenAccessible);
	}

}