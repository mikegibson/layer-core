<?php

namespace Layer\Data;

use Doctrine\ORM\EntityManagerInterface;

class DummyRepositoryFactory implements \Doctrine\ORM\Repository\RepositoryFactory {

	private $manager;

	public function __construct(RepositoryManager $manager) {
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepository(EntityManagerInterface $entityManager, $entityName) {
		return $this->manager->getRepository($entityName);
	}

}