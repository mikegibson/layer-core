<?php

namespace Layer\Data;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Layer\Data\Metadata\QueryCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RepositoryManager implements RepositoryManagerInterface {

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	private $eventDispatcher;

	/**
	 * @var ManagedRepositoryFactory
	 */
	private $factory;

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
	public function __construct(EventDispatcherInterface $eventDispatcher, QueryCollection $queryCollection) {
		$this->eventDispatcher = $eventDispatcher;
		$this->factory = new ManagedRepositoryFactory($this, $queryCollection);
	}

	/**
	 * @param EntityManagerInterface $entityManager
	 * @param $entityClass
	 * @return $this
	 */
	public function loadRepository(EntityManagerInterface $entityManager, $entityClass) {
		$repository = $this->factory->getRepository($entityManager, $entityClass);
		return $this->registerRepository($repository);
	}

	/**
	 * Register a repository
	 *
	 * @param ManagedRepositoryInterface $repository
	 * @return $this
	 * @throws \LogicException
	 */
	public function registerRepository(ManagedRepositoryInterface $repository) {
		$name = $repository->getName();
		if($this->hasRepository($name)) {
			throw new \LogicException(sprintf('Repository %s is already registered.', $name));
		}

		$event = new ManagedRepositoryEvent($repository);
		$this->eventDispatcher->dispatch(ManagedRepositoryEvent::REGISTER, $event);
		$repository = $event->getRepository();

		return $this->repositories[$name] = $repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository($name) {

		if (!$this->hasRepository($name)) {
			throw new \InvalidArgumentException(sprintf('Repository %s was not found.', $name));
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

	/**
	 * @return EventDispatcherInterface
	 */
	protected function getEventDispatcher() {
		return $this->eventDispatcher;
	}

}