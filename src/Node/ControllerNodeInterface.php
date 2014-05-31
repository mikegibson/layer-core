<?php

namespace Layer\Node;

use Symfony\Component\HttpFoundation\Request;

interface ControllerNodeInterface extends NodeInterface {

	public function getActionName();

	public function invokeAction(Request $request);

	public function getTemplate();

	public function isVisible();

	public function getVisibleChildNodes();

}