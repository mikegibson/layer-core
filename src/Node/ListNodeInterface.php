<?php

namespace Sentient\Node;

interface ListNodeInterface extends NodeInterface {

	public function areChildrenOrdered();

	public function getUrl();

}