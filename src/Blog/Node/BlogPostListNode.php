<?php

namespace Layer\Blog\Node;

use Layer\Action\SimpleAction;
use Layer\Blog\Entity\BlogPost;
use Layer\Data\ManagedRepositoryInterface;
use Layer\Data\Paginator\PaginatedNode;
use Layer\Node\ControllerNode;

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
	 * @param array $criteria
	 */
	public function __construct(ManagedRepositoryInterface $repository, array $criteria = []) {
		$this->repository = $repository;
		$this->criteria = $criteria;
	}

	public function getTemplate() {
		return '@blog/view/list_posts';
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