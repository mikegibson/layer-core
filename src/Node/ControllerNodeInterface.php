<?php

namespace Layer\Node;

use Symfony\Component\HttpFoundation\Request;

interface ControllerNodeInterface extends NodeInterface {

	/**
	 * @return string
	 */
	public function getRouteName();

	/**
	 * @return string
	 */
	public function getActionName();

	/**
	 * @return string
	 */
	public function getActionLabel();

	/**
	 * @param Request $request
	 * @return array|null|\Symfony\Component\HttpFoundation\Response
	 */
	public function invoke(Request $request);

	/**
	 * @return string
	 */
	public function getTemplate();

	/**
	 * @return bool
	 */
	public function isAccessible();

	/**
	 * @return bool
	 */
	public function isVisible();

	/**
	 * @return array
	 */
	public function getVisibleChildNodes();

}