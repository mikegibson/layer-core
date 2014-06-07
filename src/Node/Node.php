<?php

namespace Layer\Node;

class Node extends AbstractNode {

	protected $name;

	protected $label;

	protected $parentNode;

	public function __construct($name, $label, NodeInterface $parentNode) {
		$this->name = $name;
		$this->label = $label;
		$this->parentNode = $parentNode;
	}

	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getParentNode() {
		return $this->parentNode;
	}

}