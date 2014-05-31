<?php

namespace Layer\Cms\Node;

use Layer\Controller\Action\ActionInterface;
use Layer\Node\OrphanControllerNode;
use Symfony\Component\HttpFoundation\Request;

class RootCmsNode extends OrphanControllerNode {

	/**
	 * @var \Layer\Cms\Action\ActionInterface
	 */
	private $action;

	public function __construct(ActionInterface $action) {
		$this->action = $action;
	}

	public function getName() {
		return 'root';
	}

	public function getActionName() {
		return $this->getAction()->getName();
	}

	public function getLabel() {
		return 'Home';
	}

	public function getTemplate() {
		return $this->getAction()->getTemplate();
	}

	public function invokeAction(Request $request) {
		return $this->getAction()->invoke($request);
	}

	protected function getAction() {
		return $this->action;
	}

}