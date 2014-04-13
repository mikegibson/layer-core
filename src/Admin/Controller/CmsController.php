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

		$formBuilder = $this->app->form($record->toArray(), ['method' => 'PUT', 'action' => $request->getRequestUri()]);

		foreach ($dataType->fields() as $field) {
			if ($field->editable) {
				$formBuilder->add($field->name, $field->inputType);
			}
		}

		$form = $formBuilder->getForm();

		if ($request->isMethod('post')) {
			$form->handleRequest($request);
			$record->setAttributes($form->getData());
			$record->save();
		}

		$data = compact('dataType', 'record');
		$data['form'] = $form->createView();

		return $data;
	}

	/**
	 * @param DataType $dataType
	 * @param QueryBuilder $query
	 * @param array $config
	 * @return PaginatorResult
	 */
	protected function _getPaginatorResult(Application $app, DataType $dataType, Builder $query = null, array $config = []) {
		$query = $this->_getPaginatorQuery($dataType, $query);
		return new AdminPaginatorResult($app, $dataType, $query, $config);
	}

}