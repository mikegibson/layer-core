<?php

namespace Layer\Controller\Action;

use Layer\Application;
use Symfony\Component\HttpFoundation\Request;

interface ActionInterface {

	public function getName();

	public function getTemplate();

	public function invoke(Application $app, Request $request);

}