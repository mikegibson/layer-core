<?php

namespace Sentient\Node;

class Node implements NodeInterface {

	protected $childNodes = [];

	private $adopteeNodes = [];

	protected $parentNode;

	protected $name;

	protected $label;

	private $registering = false;

	public function __construct(NodeInterface $parentNode = null, $name = null, $label = null) {
		$this->parentNode = $parentNode;
		$this->name = $name;
		$this->label = $label;
	}

	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getParent() {
		return $this->parentNode;
	}

	public function getChildren() {
		$this->registerAdopters();
		return $this->childNodes;
	}

	public function hasChild($name) {
		$this->registerAdopters();
		return isset($this->childNodes[$name]);
	}

	public function getChild($name) {
		$this->registerAdopters();
		if(!isset($this->childNodes[$name])) {
			throw new \InvalidArgumentException(sprintf('Child node %s does not exist.', $name));
		}
		return $this->childNodes[$name];
	}

	public function wrapChild(
		NodeInterface $baseNode,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true,
		$overwrite = false,
		$prepend = false
	) {
		$wrappedNode = $this->createWrappedNode($baseNode, $name, $label, $baseChildrenAccessible);
		$this->registerChild($wrappedNode, $overwrite, $prepend);
		return $wrappedNode;
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null, $baseChildrenAccessible = true) {
		return new WrappedNode($baseNode, $this, $name, $label, $baseChildrenAccessible);
	}

	public function registerChild(NodeInterface $node, $overwrite = false, $prepend = false) {
		$this->registerAdopters();
		if($node->getParent() !== $this) {
			throw new \RuntimeException('Nodes being registered must return this node from their getParentNode method.');
		}
		$name = $node->getName();
		if(!$overwrite && isset($this->childNodes[$name])) {
			throw new \RuntimeException(sprintf('Node name %s is already registered.', $name));
		}
		if($prepend) {
			$this->childNodes = array_merge([$name => null], $this->getChildren(), [$name => $node]);
		} else {
			$this->childNodes[$name] = $node;
		}
	}

	public function adoptChildren(NodeInterface $node, $overwrite = false) {
		$this->adopteeNodes[] = compact('node', 'overwrite');
	}

	protected function registerAdopters() {
		if($this->registering) {
			return;
		}
		$this->registering = true;
		foreach($this->adopteeNodes as $info) {
			foreach($info['node']->getChildren() as $childNode) {
				if($info['overwrite'] || !$this->hasChild($childNode->getName())) {
					$this->wrapChild($childNode, null, null, true, $info['overwrite']);
				}
			}
		}
		$this->adopteeNodes = [];
		$this->registering = false;
	}

	public function getPath() {
		$parts = [];
		$node = $this;
		while(null !== ($parent = $node->getParent())) {
			array_unshift($parts, $node->getName());
			$node = $parent;
		}
		return implode(static::SEPARATOR, $parts);
	}

	public function getDescendent($path) {
		$parts = explode(static::SEPARATOR, $path);
		$node = $this;
		foreach($parts as $name) {
			$node = $node->getChild($name);
		}
		return $node;
	}

	public function getRoot() {
		$node = $this;
		while(($parentNode = $node->getParent()) instanceof NodeInterface) {
			$node = $parentNode;
		}
		return $node;
	}

	public function sortChildren($callback) {
		$childNodes = $this->getChildren();
		uasort($childNodes, $callback);
		$this->childNodes = $childNodes;
	}

}