<?php

namespace Layer\Data;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryCollection;

class ManagedRepository implements ManagedRepositoryInterface, Selectable {

	/**
	 * @var \Doctrine\ORM\EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var \Doctrine\ORM\EntityRepository
	 */
	private $baseRepository;

	/**
	 * @var \Doctrine\ORM\Mapping\ClassMetadata
	 */
	private $classMetadata;

	/**
	 * @var Metadata\QueryCollection
	 */
	private $queryCollection;

	/**
	 * @param ObjectRepository $baseRepository
	 * @param ClassMetadata $classMetadata
	 * @param QueryCollection $queryCollection
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		ObjectRepository $baseRepository,
		ClassMetadata $classMetadata,
		QueryCollection $queryCollection
	) {
		$this->entityManager = $entityManager;
		$this->baseRepository = $baseRepository;
		$this->classMetadata = $classMetadata;
		$this->queryCollection = $queryCollection;
	}

	public function getName() {
		return $this->queryMetadata('getEntityName');
	}

	public function getClassName() {
		return $this->getClassMetadata()->name;
	}

	public function getEntityManager() {
		return $this->entityManager;
	}

	public function createEntity() {
		return $this->getClassMetadata()->reflClass->newInstance();
	}

	public function createQueryBuilder($alias = null, $indexBy = null) {
		if($alias === null) {
			$alias = $this->getName();
		}
		return $this->baseRepository->createQueryBuilder($alias, $indexBy);
	}

	public function createResultSetMappingBuilder($alias) {
		return $this->baseRepository->createResultSetMappingBuilder($alias);
	}

	public function createNamedQuery($queryName) {
		return $this->baseRepository->createNamedQuery($queryName);
	}

	public function createNativeNamedQuery($queryName) {
		return $this->baseRepository->createNativeNamedQuery($queryName);
	}
/*
	public function createFormType(MetadataDriverInterface $driver = null) {
		if($driver === null) {
			$driver = $this->app['form.metadata_drivers.annotations'];
		}
		return new EntityFormType($this, $driver);
	}
*/
	public function clear() {
		$this->baseRepository->clear();
	}

	public function find($id, $lockMode = null, $lockVersion = null) {
		return $this->baseRepository->find($id, $lockMode, $lockVersion);
	}

	public function findAll() {
		return $this->baseRepository->findAll();
	}

	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
		return $this->baseRepository->findBy($criteria, $orderBy, $limit, $offset);
	}

	public function findOneBy(array $criteria, array $orderBy = null) {
		return $this->baseRepository->findBy($criteria, $orderBy);
	}

	/**
	 * Select all elements from a selectable that match the expression and
	 * return a new collection containing these elements.
	 *
	 * @param Criteria $criteria
	 * @return \Doctrine\Common\Collections\Collection
	 * @throws \LogicException
	 */
	public function matching(Criteria $criteria) {
		if(!$this->baseRepository instanceof Selectable) {
			throw new \LogicException('The base repository does not implement Selectable.');
		}
		return $this->baseRepository->matching($criteria);
	}

	public function getClassMetadata() {
		return $this->classMetadata;
	}

	public function getBaseRepository() {
		return $this->baseRepository;
	}

	public function queryMetadata($name, array $options = []) {
		if(!$this->queryCollection->hasQuery($name)) {
			throw new \InvalidArgumentException(sprintf('Invalid query name: %s', $name));
		}
		$query = $this->queryCollection->getQuery($name);
		return $query->getResult($this->getClassMetadata(), $options);
	}

	public function __call($method, $args) {
		$options = isset($args[0]) ? $args[0] : [];
		return $this->queryMetadata($method, $options);
	}

}