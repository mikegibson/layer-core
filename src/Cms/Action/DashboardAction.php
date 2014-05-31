<?php

namespace Layer\Cms\Action;

use Layer\Controller\Action\ActionInterface;
use Symfony\Component\HttpFoundation\Request;

class DashboardAction implements ActionInterface {

	public function getName() {
		return 'dashboard';
	}

	public function getLabel() {
		return 'Dashboard';
	}

	public function getTemplate() {
		return '@cms/view/dashboard';
	}

	public function isVisible() {
		return true;
	}

	public function invoke(Request $request) {

	}

}