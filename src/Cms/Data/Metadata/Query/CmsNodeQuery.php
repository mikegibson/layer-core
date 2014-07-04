<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Cms\Node\CmsNodeRegistry;
use Sentient\Data\ManagedRepositoryInterface;
use Sentient\Data\Metadata\QueryInterface;

abstract class CmsNodeQuery implements QueryInterface {

	/**
	 * @var \Doctrine\Common\Persistence\AbstractManagerRegistry
	 */
	private $managerRegistry;

	/**
	 * @var \Sentient\Cms\Node\CmsNodeRegistry
	 */
	private $cmsNodeRegistry;

	/**
	 * @param AbstractManagerRegistry $managerRegistry
	 * @param CmsNodeRegistry $cmsNodeRegistry
	 */
	public function __construct(AbstractManagerRegistry $managerRegistry, CmsNodeRegistry $cmsNodeRegistry) {
		$this->managerRegistry = $managerRegistry;
		$this->cmsNodeRegistry = $cmsNodeRegistry;
	}

	/**
	 * @param ClassMetadata $classMetadata
	 * @return \Doctrine\Common\Persistence\ObjectRepository
	 */
	protected function getRepository(ClassMetadata $classMetadata) {
		$repository = $this->managerRegistry->getRepository($classMetadata->getName());
		if(!$repository instanceof ManagedRepositoryInterface) {
			return false;
		}
		return $repository;
	}

	/**
	 * @return CmsNodeRegistry
	 */
	protected function getNodeRegistry() {
		return $this->cmsNodeRegistry;
	}

}