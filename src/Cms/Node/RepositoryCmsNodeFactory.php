<?php

namespace Layer\Cms\Node;

use Layer\Application;
use Layer\Cms\Action\AddAction;
use Layer\Cms\Action\EditAction;
use Layer\Cms\Action\IndexAction;
use Layer\Cms\Data\CmsRepositoryInterface;
use Layer\Controller\Action\ActionInterface;
use Layer\Node\ControllerNode;

class RepositoryCmsNodeFactory implements RepositoryCmsNodeFactoryInterface {

	protected $app;

	public function __construct(Application $app) {
		$this->app = $app;
	}

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return array|null
	 */
	public function getRepositoryCmsNodes(CmsRepositoryInterface $repository) {
		$crud = $repository->queryMetadata('getEntityCrud');
		$nodes = [];
		if($crud->read) {
			$nodes[] = $this->createIndexNode($repository);
		}
		if($crud->create) {
			$nodes[] = $this->createAddNode($repository);
		}
		if($crud->update) {
			$nodes[] = $this->createEditNode($repository);
		}
		$rootNode = null;
		foreach($nodes as $k => $node) {
			if($rootNode === null) {
				$key = $repository->getCmsSlug();
				$label = $repository->queryMetadata('getEntityHumanName', ['plural' => true, 'capitalize' => true]);
				$nodes[$k] = $rootNode = $this->getRootCmsNode()->wrapChildNode($node, $key, $label);
			} else {
				$nodes[$k] = $rootNode->wrapChildNode($node);
			}
		}
		return $nodes;
	}

	/**
	 * @param CmsRepositoryInterface $repository
	 * @return ControllerNode
	 */
	protected function createIndexNode(CmsRepositoryInterface $repository) {
		$action = new IndexAction($repository);
		return $this->createNodeFromAction($action);
	}

	protected function createAddNode(CmsRepositoryInterface $repository) {
		$action = new AddAction($repository, $this->getFormFactory(), $this->getSession(), $this->getUrlGenerator());
		return $this->createNodeFromAction($action);
	}

	protected function createEditNode(CmsRepositoryInterface $repository) {
		$action = new EditAction($repository, $this->getFormFactory(), $this->getSession(), $this->getUrlGenerator());
		return $this->createNodeFromAction($action);
	}

	protected function createNodeFromAction(ActionInterface $action) {
		return new ControllerNode('cms', $action);
	}

	/**
	 * @return \Layer\Cms\Node\RootCmsNode
	 */
	protected function getRootCmsNode() {
		return $this->app['cms.root_node'];
	}

	protected function getFormFactory() {
		return $this->app['form.factory'];
	}

	protected function getUrlGenerator() {
		return $this->app['url_generator'];
	}

	protected function getSession() {
		return $this->app['session'];
	}

}