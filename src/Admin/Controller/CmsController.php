<?php

namespace Layer\Admin\Controller;

use Illuminate\Database\Query\Builder;
use Layer\Admin\Data\AdminPaginatorResult;
use Layer\Application;
use Layer\Controller\Controller;
use Layer\Data\DataType;
use Layer\Data\SingleRecordTrait;
use Layer\Paginator\PaginatorTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminController
 *
 * @package Layer\Admin\Controller
 */
class CmsController extends Controller {

	use PaginatorTrait, SingleRecordTrait;

	/**
	 * @var string
	 */
	protected $plugin = 'admin';

	/**
	 * @param Request $request
	 * @return array
	 */
	public function indexAction(Request $request) {

		$dataType = $request->get('dataType');
		$paginator = $this->_buildPaginator($this->app, $dataType, $request);

		return compact('dataType', 'paginator');
	}

	/**
	 * @param Request $request
	 * @return array
	 */
	public function editAction(Request $request) {

		$dataType = $request->get('dataType');
		$record = $this->_getSingleRecord($dataType, $request);
		$array = $record->toArray();

		$formBuilder = $dataType->getFormBuilder('admin_edit', $array);
		$formBuilder->setAction($request->getRequestUri());

		$form = $formBuilder->getForm();

		$form->handleRequest($request);
		if ($form->isSubmitted()) {
			if($form->isValid()) {
				$record->setAttributes($form->getData());
				$record->save();
			}
		}

		$data = compact('dataType', 'record');
		$data['form'] = $form->createView();

		return $data;
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