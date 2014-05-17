<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;

class RepositoryFactory implements \Doctrine\ORM\Repository\RepositoryFactory {

	/**
	 * The list of ManagedRepository instances.
	 *
	 * @var array<ManagedRepositoryInterface>
	 */
	private $repositories = [];

	/**
	 * @param ManagedRepositoryInterface $repository
	 */
	public function addRepository(ManagedRepositoryInterface $repository) {
		$this->repositories[$repository->getName()] = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository(EntityManagerInterface $entityManager, $entityName) {

		if (!isset($this->repositories[$entityName])) {
			throw new \InvalidArgumentException(sprintf('Repository %s was not found!', $entityName));
		}

		return $this->repositories[$entityName];
	}

	public function getRepositoryList() {
		return array_keys($this->repositories);
	}

}