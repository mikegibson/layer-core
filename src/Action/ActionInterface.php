<?php

namespace Layer\Action;

use Symfony\Component\HttpFoundation\Request;

interface ActionInterface {

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @return string
	 */
	public function getTemplate();

	/**
	 * @param Request $request
	 * @return null|array|\Symfony\Component\HttpFoundation\Response
	 */
	public function invoke(Request $request);

	/**
	 * Is the action directly accessible by a get request with no additional URL parameters?
	 *
	 * @return bool
	 */
	public function isDirectlyAccessible();

	/**
	 * Should the action be visible by default in menus?
	 * Should return false if isDirectlyAccessible() returns false.
	 *
	 * @return bool
	 */
	public function isVisible();

}