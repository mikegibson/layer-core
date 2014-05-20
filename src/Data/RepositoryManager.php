<?php

namespace Layer\Data;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as BaseRepository;
use Layer\Data\Metadata\QueryCollection;

class RepositoryManager implements RepositoryManagerInterface {

	/**
	 * Base entity repository classname
	 */
	const REPOSITORY_CLASS = 'Layer\\Data\\ManagedRepository';

	/**
	 * @var DummyRepositoryFactory
	 */
	private $factory;

	/**
	 * @var Metadata\QueryCollection
	 */
	private $queryCollection;

	/**
	 * The list of ManagedRepository instances.
	 *
	 * @var array<ManagedRepositoryInterface>
	 */
	private $repositories = [];

	/**
	 * Constructor
	 * Create the repository factory
	 */
	public function __construct(QueryCollection $queryCollection) {
		$this->queryCollection = $queryCollection;
		$this->factory = new DummyRepositoryFactory($this);
	}

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param string $entityClass
	 * @param string|null $repositoryClass
	 * @throws \InvalidArgumentException
	 */
	public function loadRepository(EntityManagerInterface $entityManager, $entityClass, $repositoryClass = null) {
		if($repositoryClass === null) {
			$repositoryClass = $entityClass . 'Repository';
		}
		$reflection = new \ReflectionClass($repositoryClass);
		if(!$reflection->isSubclassOf(static::REPOSITORY_CLASS)) {
			throw new \InvalidArgumentException(
				sprintf('Class %s is not a subclass of %s', $repositoryClass, static::REPOSITORY_CLASS)
			);
		}
		$classMetadata = $entityManager->getClassMetadata($entityClass);
		$baseRepository = new BaseRepository($entityManager, $classMetadata);
		$repository = new $repositoryClass($baseRepository, $classMetadata, $this->queryCollection);
		$this->registerRepository($repository);
	}

	/**
	 * Register a repository
	 *
	 * @param ManagedRepositoryInterface $repository
	 * @throws \LogicException
	 */
	public function registerRepository(ManagedRepositoryInterface $repository) {
		$name = $repository->getName();
		if($this->hasRepository($name)) {
			throw new \LogicException(sprintf('Repository %s is already registered!', $name));
		}
		$this->repositories[$name] = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository($name) {

		if (!$this->hasRepository($name)) {
			throw new \InvalidArgumentException(sprintf('Repository %s was not found!', $name));
		}

		return $this->repositories[$name];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepositoryList() {
		return array_keys($this->repositories);
	}

	/**
	 * {@inheritdoc}
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