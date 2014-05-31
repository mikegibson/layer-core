<?php

namespace Layer\Node;

use Symfony\Component\HttpFoundation\Request;

class WrappedControllerNode extends ControllerNode {

	private $baseNode;

	private $parentNode;

	protected $name;

	protected $label;

	public function __construct(
		ControllerNodeInterface $baseNode,
		ControllerNodeInterface $parentNode,
		$name = null,
		$label = null
	) {
		$this->baseNode = $baseNode;
		$this->parentNode = $parentNode;
		$this->name = $name;
		$this->label = $label;
	}

	public function getName() {
		if($this->name !== null) {
			return $this->name;
		}
		return $this->baseNode->getName();
	}

	public function getActionName() {
		return $this->getBaseNode()->getActionName();
	}

	public function isVisible() {
		return $this->getBaseNode()->isVisible();
	}

	public function getLabel() {
		if($this->label !== null) {
			return $this->label;
		}
		return $this->baseNode->getLabel();
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

	public function getTemplate() {
		return $this->getBaseNode()->getTemplate();
	}

	public function invokeAction(Request $request) {
		$baseNode = $this->getBaseNode();
		if(!$baseNode instanceof ControllerNodeInterface) {
			throw new \RuntimeException();
		}
		return $baseNode->invokeAction($request);
	}

	protected function createWrappedNode(NodeInterface $baseNode, $name = null, $label = null) {
		return new WrappedControllerNode($baseNode, $this, $name, $label);
	}

}