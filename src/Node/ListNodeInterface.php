<?php

namespace Sentient\Node;

interface ListNodeInterface extends NodeInterface {

	public function addChild($name, $label, $url = null);

	public function areChildrenOrdered();

	public function getUrl();

}