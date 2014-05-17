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
	 * Register a repository
	 *
	 * @param ManagedRepositoryInterface $repository
	 */
	public function addRepository(ManagedRepositoryInterface $repository) {
		$this->repositories[$repository->getName()] = $repository;
	}

	/**
	 * Gets the repository class for the given name
	 *
	 * @param $name
	 * @return mixed
	 * @throws \InvalidArgumentException if repository not found
	 */
	public function getRepository($name) {

		if (!$this->hasRepository($name)) {
			throw new \InvalidArgumentException(sprintf('Repository %s was not found!', $name));
		}

		return $this->repositories[$name];
	}

	/**
	 * Get an array of registered repositories
	 *
	 * @return array
	 */
	public function getRepositoryList() {
		return array_keys($this->repositories);
	}

	/**
	 * Check if a repository is registered
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasRepository($name) {
		return isset($this->repositories[$name]);
	}

	/**
	 * Set the repository factory of a Doctrine ORM configuration
	 *
	 * @param Configuration $config
	 */
	public function initializeConfiguration(Configuration $config) {
		$config->setRepositoryFactory($this->factory);
	}

}