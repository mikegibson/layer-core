<?php

namespace Layer\Admin\Controller\Action;

use Illuminate\Database\Query\Builder;
use Layer\Admin\Data\AdminPaginatorResult;
use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Data\DataType;
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

	/**
	 * @param Application $app
	 * @param DataType $dataType
	 * @param Builder $query
	 * @param array $config
	 * @return AdminPaginatorResult
	 */
	protected function _getPaginatorResult(Application $app, DataType $dataType, Builder $query = null, array $config = []) {
		$query = $this->_getPaginatorQuery($dataType, $query);
		return new AdminPaginatorResult($app, $dataType, $query, $config);
	}

}