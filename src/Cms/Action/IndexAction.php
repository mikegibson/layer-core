<?php

namespace Layer\Cms\Action;

use Layer\Action\PaginationAction;
use Layer\Cms\Data\TableDataDecorator;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Paginator\DecoratedPaginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IndexAction extends PaginationAction {

	/**
	 * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
	 */
	private $propertyAccessor;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param PropertyAccessorInterface $propertyAccessor
	 * @param UrlGeneratorInterface $urlGenerator
	 * @param string $template
	 */
	public function __construct(
		ManagedRepositoryInterface $repository,
		PropertyAccessorInterface $propertyAccessor,
		UrlGeneratorInterface $urlGenerator,
		$template = '@cms/view/index'
	) {
		parent::__construct($repository, $template);
		$this->propertyAccessor = $propertyAccessor;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @param Request $request
	 * @return DecoratedPaginator|\Layer\Data\Paginator\Paginator
	 */
	protected function createPaginator(Request $request) {
		$paginator = parent::createPaginator($request);
		$decorator = $this->createDecorator();
		return new DecoratedPaginator($paginator, $decorator, $this->propertyAccessor);
	}

	/**
	 * @return TableDataDecorator
	 */
	protected function createDecorator() {
		return new TableDataDecorator($this->getRepository(), $this->urlGenerator);
	}

}