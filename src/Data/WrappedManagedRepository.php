<?php

namespace Layer\Data;

class WrappedManagedRepository implements ManagedRepositoryInterface {

	/**
	 * @var \Layer\Data\ManagedRepositoryInterface
	 */
	private $baseRepository;

	/**
	 * @param ManagedRepositoryInterface $baseRepository
	 */
	public function __construct(ManagedRepositoryInterface $baseRepository) {
		$this->baseRepository = $baseRepository;
	}

	public function queryMetadata($name, array $options = []) {
		return $this->getBaseRepository()->queryMetadata($name, $options);
	}

	public function getClassName() {
		return $this->getBaseRepository()->getClassName();
	}

	public function getClassMetadata() {
		return $this->getBaseRepository()->getClassMetadata();
	}

	public function getName() {
		return $this->getBaseRepository()->getName();
	}

	public function getEntityManager() {
		return $this->getBaseRepository()->getEntityManager();
	}

	public function createEntity() {
		return $this->getBaseRepository()->createEntity();
	}

	public function createQueryBuilder($alias = null, $indexBy = null) {
		return $this->getBaseRepository()->createQueryBuilder($alias, $indexBy);
	}

	public function createResultSetMappingBuilder($alias) {
		return $this->getBaseRepository()->createResultSetMappingBuilder($alias);
	}

	public function createNamedQuery($queryName) {
		return $this->getBaseRepository()->createNamedQuery($queryName);
	}

	public function createNativeNamedQuery($queryName) {
		return $this->getBaseRepository()->createNativeNamedQuery($queryName);
	}

	public function clear() {
		return $this->getBaseRepository()->clear();
	}

	public function find($id, $lockMode = null, $lockVersion = null) {
		return $this->getBaseRepository()->find($id, $lockMode, $lockVersion);
	}

	public function findAll() {
		return $this->getBaseRepository()->findAll();
	}

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->getBaseRepository()->findBy($criteria, $orderBy, $limit, $offset);
	}

	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->getBaseRepository()->findOneBy($criteria, $orderBy);
	}

	/**
	 * @return ManagedRepositoryInterface
	 */
	protected function getBaseRepository() {
		return $this->baseRepository;
	}

	public function __call($method, $args) {
		$options = isset($args[0]) ? $args[0] : [];
		return $this->queryMetadata($method, $options);
	}

}