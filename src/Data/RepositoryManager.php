<?php

namespace Layer\Data;

use Doctrine\ORM\Configuration;
use Layer\Application;

class RepositoryManager {

	/**
	 * @var \Layer\Application
	 */
	private $app;

	/**
	 * @var DummyRepositoryFactory
	 */
	private $factory;

	/**
	 * The list of ManagedRepository instances.
	 *
	 * @var array<ManagedRepositoryInterface>
	 */
	private $repositories = [];

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app) {
		$this->app = $app;
		$this->factory = new DummyRepositoryFactory($this);
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 */
	public function addRepository(ManagedRepositoryInterface $repository) {
		$this->repositories[$repository->getName()] = $repository;
	}

	/**
	 * Gets the repository for the given entity name
	 *
	 * @param $entityName
	 * @return mixed
	 * @throws \InvalidArgumentException if repository not found
	 */
	public function getRepository($entityName) {

		if (!isset($this->repositories[$entityName])) {
			throw new \InvalidArgumentException(sprintf('Repository %s was not found!', $entityName));
		}

		return $this->repositories[$entityName];
	}

	/**
	 * @return array
	 */
	public function getRepositoryList() {
		return array_keys($this->repositories);
	}

	/**
	 * @param Configuration $config
	 */
	public function initializeConfiguration(Configuration $config) {
		$config->setRepositoryFactory($this->factory);
	}

}