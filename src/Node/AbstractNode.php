<?php

namespace Layer\Node;

abstract class AbstractNode implements NodeInterface {

	protected $childNodes = [];

	public function getChildNodes() {
		return $this->childNodes;
	}

	public function hasChildNode($name) {
		return isset($this->childNodes[$name]);
	}

	public function getChildNode($name) {
		if(!$this->hasChildNode($name)) {
			throw new \InvalidArgumentException(sprintf('Child node %s does not exist.', $name));
		}
		return $this->childNodes[$name];
	}

	public function wrapChildNode(
		NodeInterface $baseNode,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true,
		$overwrite = false,
		$prepend = false
	) {
		$wrappedNode = $this->createWrappedNode($baseNode, $name, $label, $baseChildrenAccessible);
		$this->registerChildNode($wrappedNode, $overwrite, $prepend);
		return $wrappedNode;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

	public function registerChildNode(NodeInterface $node, $overwrite = false, $prepend = false) {
		if($node->getParentNode() !== $this) {
			throw new \RuntimeException('Nodes being registered must return this node from their getParentNode method.');
		}
		$name = $node->getName();
		if(!$overwrite && $this->hasChildNode($name)) {
			throw new \InvalidArgumentException(sprintf('Node name %s is already registered.', $name));
		}
		if($prepend) {
			$this->childNodes = array_merge([$name => null], $this->getChildNodes(), [$name => $node]);
		} else {
			$this->childNodes[$name] = $node;
		}
	}

	public function adoptChildNodes(NodeInterface $node, $overwrite = false) {
		foreach($node->getChildNodes() as $childNode) {
			if($overwrite || !$this->hasChildNode($childNode->getName())) {
				$this->wrapChildNode($childNode, null, null, true, $overwrite);
			}
		}
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
		$parts = explode(static::SEPARATOR, $path);
		$node = $this;
		foreach($parts as $name) {
			$node = $node->getChildNode($name);
		}
		return $node;
	}

	public function getRootNode() {
		$node = $this;
		while(($parentNode = $node->getParentNode()) instanceof NodeInterface) {
			$node = $parentNode;
		}
		return $node;
	}

	public function sortChildNodes($callback) {
		$childNodes = $this->getChildNodes();
		uasort($childNodes, $callback);
		$this->childNodes = $childNodes;
	}

}