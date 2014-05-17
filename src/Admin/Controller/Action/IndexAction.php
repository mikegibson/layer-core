<?php

namespace Layer\Admin\Controller\Action;

use Layer\Admin\Data\AdminIndexPaginator;
use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Paginator\PaginatorRequestInterface;
use Layer\Data\Paginator\PaginatorResultInterface;
use Layer\Data\Paginator\PaginatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

		$repository = $request->get('repository');

		if(!$repository instanceof ManagedRepositoryInterface) {
			return $app->abort(500);
		}

		$paginator = $this->_buildPaginator($repository, $request, $app['url_generator']);

		return compact('repository', 'paginator');

	}

	/**
	 * @param PaginatorResultInterface $result
	 * @param PaginatorRequestInterface $request
	 * @param UrlGeneratorInterface $generator
	 * @return AdminIndexPaginator
	 */
	protected function _getPaginator(
		PaginatorResultInterface $result,
		PaginatorRequestInterface $request,
		UrlGeneratorInterface $generator
	) {
		return new AdminIndexPaginator($result, $request, $generator);
	}

}