<?php

namespace Layer\Data;

use Doctrine\ORM\Configuration;
use Layer\Application;

class RepositoryManager {

	private $app;

	private $factory;

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app) {
		$this->app = $app;
		$this->factory = new RepositoryFactory();
	}

	/**
	 * @param ManagedRepositoryInterface $repository
	 */
	public function addRepository(ManagedRepositoryInterface $repository) {
		$this->factory->addRepository($repository);
	}

	/**
	 * @param $entityName
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	public function getRepository($entityName) {
		return $this->factory->getRepository($this->app['orm.em'], $entityName);
	}

	/**
	 * @return array
	 */
	public function getRepositoryList() {
		return $this->factory->getRepositoryList();
	}

	/**
	 * @param Configuration $config
	 */
	public function initializeConfiguration(Configuration $config) {
		$config->setRepositoryFactory($this->factory);
	}

}