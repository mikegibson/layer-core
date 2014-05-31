<?php

namespace Layer\Node;

class WrappedListNode extends WrappedNode implements ListNodeInterface {

	/**
	 * @param ListNodeInterface $baseNode
	 * @param ListNodeInterface $parentNode
	 * @param null $name
	 * @param null $label
	 * @param bool $baseChildrenAccessible
	 */
	public function __construct(
		ListNodeInterface $baseNode,
		ListNodeInterface $parentNode = null,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true
	) {
		parent::__construct($baseNode, $parentNode, $name, $label, $baseChildrenAccessible);
	}

	public function areChildrenOrdered() {
		return $this->getBaseNode()->areChildrenOrdered();
	}

	public function getUrl(array $params = []) {
		return $this->getBaseNode()->getUrl($params);
	}

	/**
	 * @return ListNodeInterface
	 */
	public function getBaseNode() {
		return parent::getBaseNode();
	}

	protected function createWrappedNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedListNode($baseNode, $this, $key, $label, $baseChildrenAccessible);
	}

}