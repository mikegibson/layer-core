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
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChildNodes();
		}
		$names = array_keys(array_merge($this->getBaseNode()->getChildNodes(), $this->childNodes));
		$nodes = [];
		foreach($names as $name) {
			$nodes[$name] = $this->getChildNode($name);
		}
		return $nodes;
	}

	public function hasChildNode($name) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::hasChildNode($name);
		}
		return $this->baseNode->hasChildNode($name) || isset($this->childNodes[$name]);
	}

	public function getChildNode($name) {
		if(!$this->areBaseChildrenAccessible()) {
			return parent::getChildNode($name);
		}
		if(!isset($this->childNodes[$name])) {
			$node = $this->getBaseNode()->getChildNode($name);
			$class = get_class($this);
			$this->childNodes[$name] = new $class($node, $this);
		}
		return $this->childNodes[$name];
	}

	protected function areBaseChildrenAccessible() {
		return $this->baseChildrenAccessible;
	}

}