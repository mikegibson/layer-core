<?php

namespace Layer\Data\Paginator;

use Layer\Action\PaginationAction;
use Layer\Node\ControllerNode;

abstract class PaginatedNode extends ControllerNode {

	const MODIFIER_ALL = 'all';

	protected $action;

	/**
	 * @param null $modifier
	 * @return array
	 * @throws \InvalidArgumentException
	 */
	public function getChildNodes($modifier = null) {
		$childNodes = parent::getChildNodes();
		if($modifier instanceof PaginatorInterface) {
			foreach($modifier->getData() as $entity) {
				$node = $this->createEntityNode($entity);
				$childNodes[$node->getName()] = $node;
			}
		} elseif(is_array($modifier) || $modifier === static::MODIFIER_ALL) {
			$criteria = is_array($modifier) ? $modifier : [];
			foreach($this->getRepository()->findBy($criteria) as $entity) {
				$node = $this->createEntityNode($entity);
				$childNodes[$node->getName()] = $this->wrapChildNode($node, $node->getName(), null, true, true);
			}
		} elseif($modifier !== null) {
			throw new \InvalidArgumentException('Invalid modifier.');
		}
		return $childNodes;
	}

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasChildNode($name) {
		if(parent::hasChildNode($name)) {
			return true;
		}
		return !!$this->getEntityByName($name);
	}

	public function getChildNode($name) {
		if(!isset($this->childNodes[$name]) && ($entity = $this->getEntityByName($name))) {
			$node = $this->createEntityNode($entity);
			$this->wrapChildNode($node, $name, null, true, true);
		}
		return parent::getChildNode($name);
	}

	protected function getEntityByName($name) {
		if(!$result = $this->getRepository()->findOneBy($this->getCriteria($name))) {
			return false;
		}
		return current($result);
	}

	/**
	 * @param $name
	 * @return array
	 */
	protected function getCriteria($name) {
		return [
			$this->getEntityProperty() => $name
		];
	}

	protected function getAction() {
		if($this->action === null) {
			$this->action = new PaginationAction($this->getRepository(), $this->getTemplate());
		}
		return $this->action;
	}

	/**
	 * @param $entity
	 * @return \Layer\Node\ControllerNodeInterface
	 */
	abstract protected function createEntityNode($entity);

	/**
	 * @return \Layer\Data\ManagedRepositoryInterface
	 */
	abstract protected function getRepository();

	/**
	 * @return string
	 */
	abstract protected function getEntityProperty();

}