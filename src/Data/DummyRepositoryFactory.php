<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;

class DummyRepositoryFactory implements \Doctrine\ORM\Repository\RepositoryFactory {

	/**
	 * @var RepositoryManager
	 */
	private $manager;

	/**
	 * @param RepositoryManager $manager
	 */
	public function __construct(RepositoryManager $manager) {
		$this->manager = $manager;
	}

	/**
	 * Gets the repository for a given entity name
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param string $entityName
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	public function getRepository(EntityManagerInterface $entityManager, $entityName) {
		return $this->manager->getRepository($entityName);
	}

}