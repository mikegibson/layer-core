<?php

namespace Layer\Node;

abstract class OrphanNode extends AbstractNode {

	public function getParentNode() {
		return null;
	}

}