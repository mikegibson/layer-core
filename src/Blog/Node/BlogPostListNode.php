<?php

namespace Layer\Blog\Node;

use Layer\Action\SimpleAction;
use Layer\Blog\Entity\BlogPost;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Paginator\PaginatedNode;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;

class BlogPostListNode extends PaginatedNode {

	/**
	 * @var \Layer\Data\ManagedRepositoryInterface
	 */
	private $repository;

	/**
	 * @var array
	 */
	protected $criteria;

	/**
	 * @param ManagedRepositoryInterface $repository
	 * @param string $routeName
	 * @param ControllerNodeInterface $parentNode
	 * @param null $name
	 * @param null $label
	 * @param string $template
	 * @param array $criteria
	 */
	public function __construct(
		ManagedRepositoryInterface $repository,
		$routeName = 'app',
		ControllerNodeInterface $parentNode = null,
		$name = null,
		$label = null,
		$template = '@blog/view/list_posts',
		array $criteria = []
	) {
		parent::__construct($routeName, null, $parentNode, $name, $label, $template, true, true);
		$this->repository = $repository;
		$this->criteria = $criteria;
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	protected function getRepository() {
		return $this->repository;
	}

	/**
	 * @return string
	 */
	protected function getEntityProperty() {
		return 'slug';
	}

	/**
	 * @param $post
	 * @return ControllerNode|\Layer\Node\ControllerNodeInterface
	 * @throws \RuntimeException
	 */
	protected function createEntityNode($post) {
		if(!$post instanceof BlogPost) {
			throw new \RuntimeException('The entity must be a blog post.');
		}
		$action = new SimpleAction('view', (string) $post, '@blog/view/view_post', function() use($post) {
			return compact('post');
		});
		return new ControllerNode('app', $action);
	}

}