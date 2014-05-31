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

	public function wrapChildNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		$wrappedNode = $this->createWrappedNode($baseNode, $key, $label, $baseChildrenAccessible);
		$this->registerChildNode($wrappedNode);
		return $wrappedNode;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $key = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedNode($baseNode, $this, $key, $label, $baseChildrenAccessible);
	}

	public function registerChildNode(NodeInterface $node, $overwrite = false, $prepend = false) {
		if($node->getParentNode() !== $this) {
			throw new \RuntimeException('Nodes being registered must return this node from their getParentNode method.');
		}
		$key = $node->getKey();
		if(!$overwrite && $this->hasChildNode($key)) {
			throw new \InvalidArgumentException(sprintf('Node key %s is already registered.', $key));
		}
		if($prepend) {
			$this->childNodes = array_merge([$key => null], $this->getChildNodes(), [$key => $node]);
		} else {
			$this->childNodes[$key] = $node;
		}
	}

	public function getPath() {
		$parts = [];
		$node = $this;
		while(null !== ($parent = $node->getParentNode())) {
			array_unshift($parts, $node->getKey());
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

	public function sortChildNodes($callback) {
		$childNodes = $this->getChildNodes();
		uasort($childNodes, $callback);
		$this->childNodes = $childNodes;
	}

}