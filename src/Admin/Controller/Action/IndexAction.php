<?php

namespace Layer\Admin\Controller\Action;

use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Paginator\PaginatorTrait;
use Symfony\Component\HttpFoundation\Request;

class IndexAction implements ActionInterface {

	use PaginatorTrait;

	public function getName() {
		return 'index';
	}

	public function getTemplate() {
		return '@admin/cms/index';
	}

	/**
	 * @param Application $app
	 * @param Request $request
	 * @return array
	 */
	public function invoke(Application $app, Request $request) {

		$dataType = $request->get('dataType');
		$paginator = $this->_buildPaginator($app, $dataType, $request);

		return compact('dataType', 'paginator');

	}

}