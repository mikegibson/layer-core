<?php

namespace Layer\Controller\Action;

use Symfony\Component\HttpFoundation\Request;

interface ActionInterface {

	public function getName();

	public function getLabel();

	public function getTemplate();

	public function invoke(Request $request);

	public function isVisible();

}