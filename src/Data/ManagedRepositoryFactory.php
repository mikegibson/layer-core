<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Layer\Data\Metadata\QueryCollection;

class ManagedRepositoryFactory implements RepositoryFactory {

	/**
	 * Base entity repository classname
	 */
	const REPOSITORY_CLASS = 'Layer\\Data\\ManagedRepository';

	private $queryCollection;

	private $repositories = [];

	/**
	 * @param RepositoryManagerInterface $repositoryManager
	 */
	public function __construct(QueryCollection $queryCollection) {
		$this->queryCollection = $queryCollection;
	}

	/**
	 * Gets the repository for a given entity name
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param string $className
	 * @return ManagedRepository
	 */
	public function getRepository(EntityManagerInterface $entityManager, $className) {
		if(!isset($this->repositories[$className])) {
			$this->repositories[$className] = $this->generateRepository($entityManager, $className);
		}
		return $this->repositories[$className];
	}

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param $className
	 * @return mixed
	 */
	protected function generateRepository(EntityManagerInterface $entityManager, $className) {
		$classMetadata = $entityManager->getClassMetadata($className);
		$baseRepository = new EntityRepository($entityManager, $classMetadata);
		$repositoryClass = static::REPOSITORY_CLASS;
		$repository = new $repositoryClass($entityManager, $baseRepository, $classMetadata, $this->queryCollection);
		return $repository;
	}

}