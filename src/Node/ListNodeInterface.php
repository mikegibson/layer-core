<?php

namespace Layer\Node;

interface ListNodeInterface extends NodeInterface {

	public function areChildrenOrdered();

	public function getUrl();

}