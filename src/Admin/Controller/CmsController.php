<?php

namespace Layer\Admin\Controller;

use Layer\Admin\Paginator\CmsPaginator;
use Layer\Application;
use Layer\Controller\Controller;
use Layer\Data\DataType;
use Layer\Data\SingleRecordTrait;
use Layer\Paginator\PaginatorRequestInterface;
use Layer\Paginator\PaginatorResultInterface;
use Layer\Paginator\PaginatorTrait;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormView;
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
        $paginator = $this->_buildPaginator($dataType, $request);

        return compact('dataType', 'paginator');
    }

    public function editAction(Request $request) {

        $dataType = $request->get('dataType');
        $record = $this->_getSingleRecord($dataType, $request);

        $formBuilder = $this->app->form($record->toArray());

        foreach($dataType->fields() as $field) {
            if($field->editable) {
                $formBuilder->add($field->name, $field->inputType);
            }
        }

        $form = $formBuilder->getForm();

        if($request->isMethod('post')) {
            $form->handleRequest($request);
            $record->setAttributes($form->getData());
            $record->save();
        }

        return compact('dataType', 'form', 'record');

    }

    /**
     * @param DataType $dataType
     * @param PaginatorRequestInterface $request
     * @param PaginatorResultInterface $result
     * @return CmsPaginator
     */
    protected function _getPaginator(
		Application $app,
        DataType $dataType,
        PaginatorRequestInterface $request,
        PaginatorResultInterface $result
    ) {
        return new CmsPaginator($app, $dataType, $request, $result);
    }

}