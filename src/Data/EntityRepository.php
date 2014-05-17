<?php

namespace Layer\Data;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Application;

abstract class EntityRepository implements ManagedRepositoryInterface, Selectable {

	protected $app;

	protected $metadata;

	protected $repository;

	public function __construct(Application $app) {
		$this->app = $app;
		$this->metadata = $app['orm.em']->getClassMetadata($this->getClassName());
		$this->repository = new \Doctrine\ORM\EntityRepository($app['orm.em'], $this->metadata);
	}

	public function createEntity() {
		return $this->getEntityMetadata()->reflClass->newInstance();
	}

	public function createQueryBuilder($alias = null, $indexBy = null) {
		if($alias === null) {
			$alias = $this->getName();
		}
		return $this->repository->createQueryBuilder($alias, $indexBy);
	}

	public function createResultSetMappingBuilder($alias) {
		return $this->repository->createResultSetMappingBuilder($alias);
	}

	public function createNamedQuery($queryName) {
		return $this->repository->createNamedQuery($queryName);
	}

	public function createNativeNamedQuery($queryName) {
		return $this->repository->createNativeNamedQuery($queryName);
	}

	public function clear() {
		$this->repository->clear();
	}

	public function find($id, $lockMode = null, $lockVersion = null) {
		return $this->repository->find($id, $lockMode, $lockVersion);
	}

	public function findAll() {
		return $this->repository->findAll();
	}

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
	}

	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->repository->findBy($criteria, $orderBy);
	}

	/**
	 * Select all elements from a selectable that match the expression and
	 * return a new collection containing these elements.
	 *
	 * @param \Doctrine\Common\Collections\Criteria $criteria
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function matching(Criteria $criteria) {
		return $this->repository->matching($criteria);
	}

	public function getEntityMetadata() {
		return $this->metadata;
	}

	public function getBaseRepository() {
		return $this->repository;
	}

	public function getClassName() {
		return preg_replace('/(Repository)$/', '', get_class($this));
	}

	public function getBaseEntityName() {
		$parts = explode('\\', $this->getClassName());
		return $this->app['inflector']->underscore(array_pop($parts));
	}

	public function getNamespace() {
		$parts = explode('\\', get_class($this));
		$count = count($parts);
		if($count > 1) {
			return $this->app['inflector']->underscore($parts[$count - 2]);
		}
	}

	public function getBaseName() {
		return $this->app['inflector']->pluralize($this->getBaseEntityName());
	}

	public function getName() {
		return $this->getNamespace() . ':' . $this->getBaseName();
	}

	public function getSingularHumanName() {
		return $this->app['inflector']->humanize($this->getBaseEntityName());
	}

	public function getPluralHumanName() {
		return $this->app['inflector']->pluralize($this->getSingularHumanName());
	}

	public function getEditableFields() {
		$methods = $this->getEntityMetadata()->reflClass->getMethods(\ReflectionMethod::IS_PUBLIC);
		$fields = [];
		foreach($methods as $method) {
			if(preg_match('/^set([A-Z][A-Za-z]+)/', $method->name, $matches)) {
				$fields[] = lcfirst($matches[1]);
			}
		}
		return $fields;
	}

	public function __call($method, $args) {
		return call_user_func_array([$this->repository, $method], $args);
	}

}