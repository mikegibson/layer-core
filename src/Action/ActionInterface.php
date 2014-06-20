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
	 * @return bool
	 */
	public function isVisible();

}