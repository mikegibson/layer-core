<?php

namespace Layer\Node;

class WrappedNode extends AbstractNode {

	private $baseNode;

	private $parentNode;

	protected $name;

	protected $label;

	public function __construct(NodeInterface $baseNode, NodeInterface $parentNode, $name = null, $label = null) {
		$this->baseNode = $baseNode;
		$this->parentNode = $parentNode;
		$this->name = $name;
		$this->label = $label;
	}

	public function getName() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->getBaseNode()->getName();
	}

	public function getLabel() {
		if($this->label !== null) {
			return $this->label;
		}
		return $this->getBaseNode()->getLabel();
	}

	public function getBaseNode() {
		return $this->baseNode;
	}

	public function getParentNode() {
		return $this->parentNode;
	}

	public function getChildNodes() {
		$keys = array_keys(array_merge($this->baseNode->getChildNodes(), $this->childNodes));
		$nodes = [];
		foreach($keys as $key) {
			$nodes[$key] = $this->getChildNode($key);
		}
		return $nodes;
	}

	public function hasChildNode($key) {
		return $this->baseNode->hasChildNode($key) || isset($this->childNodes[$key]);
	}

	public function getChildNode($key) {
		if(!isset($this->childNodes[$key])) {
			$node = $this->baseNode->getChildNode($key);
			$class = get_class($this);
			$this->childNodes[$key] = new $class($node, $this);
		}
		return $this->childNodes[$key];
	}

}