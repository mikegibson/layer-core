<?php

namespace Sentient\Node;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ControllerNodeListNode extends ListNode {

	/**
	 * @var ControllerNodeInterface
	 */
	private $controllerNode;

	private $routeName;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	private $controllerNodeChildrenAccessible;

	private $initializing = false;

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @param string $routeName
	 * @param UrlGeneratorInterface $urlGenerator
	 * @param ListNodeInterface $parentNode
	 * @param bool $controllerNodeChildrenAccessible
	 */
	public function __construct(
		ControllerNodeInterface $controllerNode,
		$routeName,
		UrlGeneratorInterface $urlGenerator,
		ListNodeInterface $parentNode = null,
		$controllerNodeChildrenAccessible = true
	) {
		$this->controllerNode = $controllerNode;
		$this->routeName = $routeName;
		$this->urlGenerator = $urlGenerator;
		$this->parentNode = $parentNode;
		$this->controllerNodeChildrenAccessible = $controllerNodeChildrenAccessible;
	}

	public function getLabel() {
		return $this->getControllerNode()->getLabel();
	}

	public function getParent() {
		return $this->parentNode;
	}

	public function getName() {
		return $this->getControllerNode()->getName();
	}

	public function getUrl(array $params = []) {
		$controllerNode = $this->getControllerNode();
		if($controllerNode->isDirectlyAccessible() || $controllerNode->isPassthrough()) {
			$params['node'] = $controllerNode->getPath();
			return $this->getUrlGenerator()->generate($this->routeName, $params);
		}
		return false;
	}

	public function hasChild($name) {
		$this->initControllerNodeChildren();
		return parent::hasChild($name);
	}

	public function getChild($name) {
		$this->initControllerNodeChildren();
		return parent::getChild($name);
	}

	public function getChildren() {
		$this->initControllerNodeChildren();
		return parent::getChildren();
	}

	public function registerChild(NodeInterface $node, $overwrite = false, $prepend = false) {
		$this->initControllerNodeChildren();
		return parent::registerChild($node, $overwrite, $prepend);
	}

	protected function registerControllerChildNode($name, $childrenAccessible = true) {
		if(
			!parent::hasChild($name) &&
			$this->areControllerNodeChildrenAccessible() &&
			$this->getControllerNode()->hasChild($name) &&
			$this->getControllerNode()->getChild($name)->isVisible()
		) {
			$listNode = $this->createListNode($this->getControllerNode()->getChild($name, $childrenAccessible));
			$this->registerChild($listNode);
			return true;
		}
		return false;
	}

	protected function initControllerNodeChildren() {
		if($this->initializing) {
			return;
		}
		$this->initializing = true;
		if($this->areControllerNodeChildrenAccessible()) {
			foreach($this->getControllerNode()->getChildren() as $key => $node) {
				$this->registerControllerChildNode($key);
			}
		}
		$this->initializing = false;
	}

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @param bool $childrenAccessible
	 * @return ControllerNodeListNode
	 */
	protected function createListNode(ControllerNodeInterface $controllerNode, $childrenAccessible = true) {
		return new ControllerNodeListNode($controllerNode, $this->routeName, $this->getUrlGenerator(), $this, $childrenAccessible);
	}

	/**
	 * @return ControllerNodeInterface
	 */
	protected function getControllerNode() {
		return $this->controllerNode;
	}

	protected function getUrlGenerator() {
		return $this->urlGenerator;
	}

	protected function areControllerNodeChildrenAccessible() {
		return $this->controllerNodeChildrenAccessible;
	}

}