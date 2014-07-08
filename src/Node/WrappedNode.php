<?php

namespace Sentient\Node;

class WrappedNode extends Node {

	private $baseNode;

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

	public function getChildren() {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChildren();
		}
		$names = array_keys(array_merge($this->getBaseNode()->getChildren(), $this->childNodes));
		$nodes = [];
		foreach($names as $name) {
			$nodes[$name] = $this->getChild($name);
		}
		return $nodes;
	}

	public function hasChild($name) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::hasChild($name);
		}
		return $this->baseNode->hasChild($name) || isset($this->childNodes[$name]);
	}

	public function getChild($name) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChild($name);
		}
		if(!isset($this->childNodes[$name])) {
			$node = $this->getBaseNode()->getChild($name);
			$class = get_class($this);
			$this->childNodes[$name] = new $class($node, $this);
		}
		return $this->childNodes[$name];
	}

	protected function areBaseChildrenAccessible() {
		return $this->baseChildrenAccessible;
	}

}