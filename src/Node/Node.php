<?php

namespace Layer\Node;

class Node extends AbstractNode {

	protected $key;

	protected $label;

	protected $parentNode;

	public function __construct($key, $label, NodeInterface $parentNode) {
		$this->key = $key;
		$this->label = $label;
		$this->parentNode = $parentNode;
	}

	public function getName() {
		return $this->key;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getParentNode() {
		return $this->parentNode;
	}

}