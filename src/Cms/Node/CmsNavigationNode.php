<?php

namespace Layer\Cms\Node;

use Layer\Node\ControllerNodeInterface;
use Layer\Node\ControllerNodeListNode;

class CmsNavigationNode extends ControllerNodeListNode {

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @return ControllerNodeListNode
	 */
	protected function createListNode(ControllerNodeInterface $controllerNode, $areChildrenAccessible = true) {
		return new CmsNavigationNode($controllerNode, $this->getUrlGenerator(), $this, $areChildrenAccessible);
	}

}