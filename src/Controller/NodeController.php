<?php

namespace Layer\Controller;

use Layer\Node\ControllerNodeInterface;
use Layer\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeController {

	private $rootNode;

	private $view;

	/**
	 * @param ControllerNodeInterface $rootNode
	 * @param ViewInterface $view
	 */
	public function __construct(ControllerNodeInterface $rootNode, ViewInterface $view) {
		$this->rootNode = $rootNode;
		$this->view = $view;
	}

	public function dispatch(Request $request) {
		$rootNode = $this->getRootNode();
		$nodePath = $request->get('node');
		$node = $rootNode->getDescendent($nodePath);
		if(!$node instanceof ControllerNodeInterface) {
			throw new \RuntimeException();
		}
		$template = $node->getTemplate();
		$data = $node->invokeAction($request) ?: [];
		if($data instanceof Response) {
			return $data;
		}
		$data = array_merge($data, compact('rootNode', 'nodePath', 'node'));
		return $this->getView()->render($template, $data);
	}

	protected function getRootNode() {
		return $this->rootNode;
	}

	protected function getView() {
		return $this->view;
	}

}