<?php

namespace Sentient\Node;

class ListNode extends Node implements ListNodeInterface {

	private $url;

	public function __construct(ListNodeInterface $parentNode = null, $name = null, $label = null, $url = null) {
		parent::__construct($parentNode, $name, $label);
		$this->url = $url;
	}

	public function areChildrenOrdered() {
		return false;
	}

	public function getUrl(array $params = null) {
		return $this->url;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedListNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

}