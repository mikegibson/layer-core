<?php

namespace Layer\Node;

class WrappedNode extends AbstractNode {

	private $baseNode;

	private $parentNode;

	private $name;

	private $label;

	private $baseChildrenAccessible;

	public function __construct(
		NodeInterface $baseNode,
		NodeInterface $parentNode = null,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true
	) {
		$this->baseNode = $baseNode;
		$this->parentNode = $parentNode;
		$this->name = $name;
		$this->label = $label;
		$this->baseChildrenAccessible = $baseChildrenAccessible;
	}

	public function getKey() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->getBaseNode()->getKey();
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
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChildNodes();
		}
		$keys = array_keys(array_merge($this->getBaseNode()->getChildNodes(), $this->childNodes));
		$nodes = [];
		foreach($keys as $key) {
			$nodes[$key] = $this->getChildNode($key);
		}
		return $nodes;
	}

	public function hasChildNode($key) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::hasChildNode($key);
		}
		return $this->baseNode->hasChildNode($key) || isset($this->childNodes[$key]);
	}

	public function getChildNode($key) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChildNode($key);
		}
		if(!isset($this->childNodes[$key])) {
			$node = $this->getBaseNode()->getChildNode($key);
			$class = get_class($this);
			$this->childNodes[$key] = new $class($node, $this);
		}
		return $this->childNodes[$key];
	}

	protected function areBaseChildrenAccessible() {
		return $this->baseChildrenAccessible;
	}

}