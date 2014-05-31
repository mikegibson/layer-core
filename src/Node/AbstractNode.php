<?php

namespace Layer\Node;

abstract class AbstractNode implements NodeInterface {

	protected $childNodes = [];

	public function getChildNodes() {
		return $this->childNodes;
	}

	public function hasChildNode($key) {
		return isset($this->childNodes[$key]);
	}

	public function getChildNode($key) {
		if(!$this->hasChildNode($key)) {
			throw new \InvalidArgumentException(sprintf('Child node %s does not exist.', $key));
		}
		return $this->childNodes[$key];
	}

	public function wrapChildNode(NodeInterface $baseNode, $name = null, $label = null) {
		$wrappedNode = $this->createWrappedNode($baseNode, $name, $label);
		$this->registerChildNode($wrappedNode);
		return $wrappedNode;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null) {
		return new WrappedNode($baseNode, $this, $name, $label);
	}

	public function registerChildNode(NodeInterface $node, $overwrite = false) {
		if($node->getParentNode() !== $this) {
			throw new \RuntimeException('Nodes being registered must return this node from their getParentNode method.');
		}
		$key = $node->getName();
		if(!$overwrite && $this->hasChildNode($key)) {
			throw new \InvalidArgumentException(sprintf('Node key %s is already registered.', $key));
		}
		$this->childNodes[$key] = $node;
	}

	public function getPath() {
		$parts = [];
		$node = $this;
		while(null !== ($parent = $node->getParentNode())) {
			array_unshift($parts, $node->getName());
			$node = $parent;
		}
		return implode(static::SEPARATOR, $parts);
	}

	public function getDescendent($path) {
		$keys = explode(static::SEPARATOR, $path);
		$node = $this;
		foreach($keys as $key) {
			$node = $node->getChildNode($key);
		}
		return $node;
	}

}