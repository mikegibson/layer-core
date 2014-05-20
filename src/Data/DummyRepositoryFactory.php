<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;

class DummyRepositoryFactory implements RepositoryFactory {

	/**
	 * @var RepositoryManager
	 */
	private $repositoryManager;

	/**
	 * @param RepositoryManagerInterface $repositoryManager
	 */
	public function __construct(RepositoryManagerInterface $repositoryManager) {
		$this->repositoryManager = $repositoryManager;
	}

	/**
	 * Gets the repository for a given entity name
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param string $entityName
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	public function getRepository(EntityManagerInterface $entityManager, $entityName) {
		return $this->repositoryManager->getRepository($entityName);
	}

}