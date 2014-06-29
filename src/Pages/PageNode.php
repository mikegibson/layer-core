<?php

namespace Layer\Pages;

use Layer\Data\ManagedRepositoryInterface;
use Layer\Node\ControllerNode;
use Layer\Node\ControllerNodeInterface;
use Symfony\Component\HttpFoundation\Request;

class PageNode extends ControllerNode {

	private $repository;

	private $page;

	public function __construct(
		ManagedRepositoryInterface $repository,
		Page $page = null,
		ControllerNodeInterface $parentNode = null
	) {
		$this->repository = $repository;
		$this->page = $page;
		$this->parentNode = $parentNode;
	}

	protected function getRepository() {
		return $this->repository;
	}

	public function getPage() {
		return $this->page;
	}

	protected function getChildPages(array $criteria = []) {
		$criteria['parent'] = $this->getPageId();
		return $this->getRepository()->findBy($criteria);
	}

	protected function getChildPage($slug) {
		$result = $this->getChildPages(['slug' => $slug]);
		return isset($result[0]) ? $result[0] : null;
	}

	public function getChildNode($key) {
		if(!isset($this->childNodes[$key])) {
			$page = $this->getChildPage($key);
			if($page instanceof Page) {
				$this->initializeChildPage($page);
			}
		}
		return parent::getChildNode($key);
	}

	public function getChildNodes() {
		static $initialized = false;
		if(!$initialized) {
			foreach($this->getChildPages() as $page) {
				$this->initializeChildPage($page);
			}
			$initialized = true;
		}
		return parent::getChildNodes();
	}

	protected function initializeChildPage(Page $page) {
		if(isset($this->childNodes[$page->getSlug()])) {
			return;
		}
		$node = new PageNode($this->getRepository(), $page, $this);
		$this->registerChildNode($node);
	}

	public function getRouteName() {
		return 'pages';
	}

	public function getActionName() {
		return 'view';
	}

	public function getActionLabel() {
		return 'View page';
	}

	public function getName() {
		$page = $this->getPage();
		if($page === null) {
			return $this->getRouteName();
		}
		return $page->getSlug();
	}

	public function getLabel() {
		$page = $this->getPage();
		if($page === null) {
			return 'Pages';
		}
		return $page->getTitle();
	}

	protected function getPageId() {
		$page = $this->getPage();
		if($page === null) {
			return null;
		}
		return $page->getId();
	}

	public function getTemplate() {
		return '@pages/view';
	}

	public function isAccessible() {
		return !!$this->getPage();
	}

	public function isVisible() {
		return !!$this->getPage();
	}

	public function invoke(Request $request) {
		return ['page' => $this->getPage()];
	}

}