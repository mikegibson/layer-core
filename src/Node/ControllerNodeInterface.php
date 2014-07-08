<?php

namespace Sentient\Node;

use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

interface ControllerNodeInterface extends NodeInterface {

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
	public function isDirectlyAccessible();

	/**
	 * @return bool
	 */
	public function isVisible();

	/**
	 * @return bool
	 */
	public function isPassthrough();

	/**
	 * @return array
	 */
	public function getVisibleChildren();

	/**
	 * @param ControllerCollection $controllers
	 */
	public function mountControllers(ControllerCollection $controllers);

	/**
	 * @param ControllerCollection $controllers
	 */
	public function connectControllers(ControllerCollection $controllers);

}