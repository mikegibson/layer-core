<?php

namespace Sentient\Cms\Node;

use Sentient\Node\ControllerNodeInterface;
use Sentient\Node\ControllerNodeListNode;

class CmsNavigationNode extends ControllerNodeListNode {

	/**
	 * @param ControllerNodeInterface $controllerNode
	 * @param bool $childrenAccessible
	 * @return CmsNavigationNode
	 */
	protected function createListNode(ControllerNodeInterface $controllerNode, $childrenAccessible = true) {
		return new CmsNavigationNode($controllerNode, 'cms', $this->getUrlGenerator(), $this, $childrenAccessible);
	}

}