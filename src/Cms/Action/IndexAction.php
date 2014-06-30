<?php

namespace Sentient\Cms\Action;

use Sentient\Action\PaginationAction;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\Paginator\DecoratedPaginator;
use Sentient\Data\TableData\TableDataDecoratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class IndexAction extends PaginationAction {

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $decorator;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param PropertyAccessorInterface $propertyAccessor
	 * @param TableDataDecoratorInterface $decorator
	 * @param string $template
	 */
	public function __construct(
		ManagedRepositoryInterface $repository,
		PropertyAccessorInterface $propertyAccessor,
		TableDataDecoratorInterface $decorator,
		$template = '@cms/view/index'
	) {
		parent::__construct($repository, $template);
		$this->propertyAccessor = $propertyAccessor;
		$this->decorator = $decorator;
	}

	/**
	 * @param Request $request
	 * @return DecoratedPaginator|\Sentient\Data\Paginator\Paginator
	 */
	protected function createPaginator(Request $request) {
		return new DecoratedPaginator(parent::createPaginator($request), $this->decorator, $this->propertyAccessor);
	}

}