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

	public function getParentNode() {
		return $this->parentNode;
	}

	public function getName() {
		return $this->getControllerNode()->getName();
	}

	public function getUrl(array $params = []) {
		$controllerNode = $this->getControllerNode();
		if($controllerNode->isDirectlyAccessible()) {
			$params['node'] = $controllerNode->getPath();
			return $this->getUrlGenerator()->generate($this->routeName, $params);
		}
		return false;
	}

	public function hasChildNode($name) {
		$this->initControllerNodeChildren();
		return parent::hasChildNode($name);
	}

	public function getChildNode($name) {
		$this->initControllerNodeChildren();
		return parent::getChildNode($name);
	}

	public function getChildNodes() {
		$this->initControllerNodeChildren();
		return parent::getChildNodes();
	}

	public function registerChildNode(NodeInterface $node, $overwrite = false, $prepend = false) {
		$this->initControllerNodeChildren();
		return parent::registerChildNode($node, $overwrite, $prepend);
	}

	protected function registerControllerChildNode($name, $childrenAccessible = true) {
		if(
			!parent::hasChildNode($name) &&
			$this->areControllerNodeChildrenAccessible() &&
			$this->getControllerNode()->hasChildNode($name) &&
			$this->getControllerNode()->getChildNode($name)->isVisible()
		) {
			$listNode = $this->createListNode($this->getControllerNode()->getChildNode($name, $childrenAccessible));
			$this->registerChildNode($listNode);
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
			foreach($this->getControllerNode()->getChildNodes() as $key => $node) {
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