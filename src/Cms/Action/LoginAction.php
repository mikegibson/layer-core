<?php

namespace Layer\Cms\Action;

use Symfony\Component\HttpFoundation\Request;

class LoginAction extends \Layer\Users\Action\LoginAction {

	public function getTemplate() {
		return '@cms/view/login';
	}

	protected function getFormAction(Request $request) {
		return '/cms/login-check';
	}

}