<?php

namespace Layer\Pages;

use Layer\Controller\Controller;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PagesController extends Controller {

	public function viewAction(Request $request) {
		$page = $request->get('page');
		return compact('page');
	}

}