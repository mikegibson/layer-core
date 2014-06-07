<?php

namespace Layer\Node;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ControllerNodeListNode extends ListNode {

	/**
	 * @var ControllerNodeInterface
	 */
	private $controllerNode;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @var ListNodeInterface|null
	 */
	private $parentNode;

	private $controllerNodeChildrenAccessible;

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @param UrlGeneratorInterface $urlGenerator
	 * @param ListNodeInterface $parentNode
	 * @param bool $controllerNodeChildrenAccessible
	 */
	public function __construct(
		ControllerNodeInterface $controllerNode,
		UrlGeneratorInterface $urlGenerator,
		ListNodeInterface $parentNode = null,
		$controllerNodeChildrenAccessible = true
	) {
		$this->controllerNode = $controllerNode;
		$this->urlGenerator = $urlGenerator;
		$this->parentNode = $parentNode;
		$this->controllerNodeChildrenAccessible = $controllerNodeChildrenAccessible;
		$this->initialize();
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
		$params['node'] = $controllerNode->getPath();
		return $this->getUrlGenerator()->generate($controllerNode->getRouteName(), $params);
	}

	protected function initialize() {
		if($this->areControllerNodeChildrenAccessible()) {
			foreach($this->getControllerNode()->getVisibleChildNodes() as $controllerNode) {
				$listNode = $this->createListNode($controllerNode);
				$this->registerChildNode($listNode, true);
			}
		}
	}

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @param bool $areChildrenAccessible
	 * @return ControllerNodeListNode
	 */
	protected function createListNode(ControllerNodeInterface $controllerNode, $areChildrenAccessible = true) {
		return new ControllerNodeListNode($controllerNode, $this->getUrlGenerator(), $this, $areChildrenAccessible);
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