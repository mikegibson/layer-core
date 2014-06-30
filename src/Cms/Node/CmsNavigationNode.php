<?php

namespace Sentient\Cms\Node;

use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\ControllerNodeListNode;

class CmsNavigationNode extends ControllerNodeListNode {

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @return ControllerNodeListNode
	 */
	protected function createListNode(ControllerNodeInterface $controllerNode, $areChildrenAccessible = true) {
		return new CmsNavigationNode($controllerNode, $this->getUrlGenerator(), $this, $areChildrenAccessible);
	}

}