<?php

namespace Layer\Admin\Controller\Action;

use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class TestAction implements ActionInterface {

	public function getName() {
		return 'test';
	}

	public function getTemplate() {
		return '@admin/cms/test.twig';
	}

	public function invoke(Application $app, Request $request) {

	}

}