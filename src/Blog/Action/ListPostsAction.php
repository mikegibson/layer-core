<?php

namespace Sentient\Blog\Action;

use Sentient\Action\PaginationAction;
use Sentient\Data\ManagedRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ListPostsAction extends PaginationAction {

	protected $blogCategories;

	public function __construct(
		ManagedRepositoryInterface $blogPosts,
		ManagedRepositoryInterface $blogCategories,
		$template
	) {
		parent::__construct($blogPosts, $template);
		$this->blogCategories = $blogCategories;
	}

	public function invoke(Request $request) {
		$result = parent::invoke($request);
		if($category = $request->get('category')) {
			$result['category'] = $category;
		}
		$result['categories'] = $this->blogCategories->findAll();
		return $result;
	}

	protected function createQueryBuilder(Request $request) {
		$queryBuilder = parent::createQueryBuilder($request);
		$select = current($queryBuilder->getRootAliases());
		if($category = $request->get('category')) {
			$queryBuilder
				->add('where', "$select.category = :categoryId")
				->setParameter('categoryId', $category->getId());
		}
		return $queryBuilder;
	}

}