<?php

namespace Layer\Cms\Node;

use Layer\Action\ActionInterface;
use Layer\Application;
use Layer\Cms\Action\AddAction;
use Layer\Cms\Action\EditAction;
use Layer\Cms\Action\IndexAction;
use Layer\Cms\Data\CmsRepositoryInterface;
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
		if(!$crud->read) {
			return [];
		}
		$nodes = [];
		$rootNode = $this->getRootCmsNode();
		$indexNode = $this->createIndexNode($repository);
		$name = $repository->getCmsSlug();
		$label = $repository->queryMetadata('getEntityHumanName', ['plural' => true, 'capitalize' => true]);
		$nodes[] = $repositoryRoot = $rootNode->wrapChildNode($indexNode, $name, $label);
		if($crud->create) {
			$nodes[] = $repositoryRoot->wrapChildNode($this->createAddNode($repository));
		}
		if($crud->update) {
			$nodes[] = $repositoryRoot->wrapChildNode($this->createEditNode($repository));
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
		$action = new AddAction($repository, $this->getFormFactory(), $this->getUrlGenerator());
		return $this->createNodeFromAction($action);
	}

	protected function createEditNode(CmsRepositoryInterface $repository) {
		$action = new EditAction($repository, $this->getFormFactory(), $this->getUrlGenerator());
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

}