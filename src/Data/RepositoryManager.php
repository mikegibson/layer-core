<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;
use Layer\Application;

class RepositoryManager implements \Doctrine\ORM\Repository\RepositoryFactory {

	private $app;

	/**
	 * The list of ManagedRepository instances.
	 *
	 * @var array<ManagedRepositoryInterface>
	 */
	private $repositories = [];

	public function __construct(Application $app) {
		$this->app = $app;
	}

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

}