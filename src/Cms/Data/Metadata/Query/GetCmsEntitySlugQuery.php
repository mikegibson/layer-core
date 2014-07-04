<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\Query\GetEntityNameQuery;
use Sentient\Data\Metadata\QueryInterface;

/**
 * Class GetCmsEntitySlugQuery
 * @package Sentient\Cms\Data\Metadata\Query
 */
class GetCmsEntitySlugQuery implements QueryInterface {

	/**
	 * @var GetCmsEntityQuery
	 */
	protected $reader;

	/**
	 * @var \Sentient\Data\Metadata\Query\GetEntityNameQuery
	 */
	protected $entityNameQuery;

	/**
	 * @param Reader $reader
	 * @param GetEntityNameQuery $entityNameQuery
	 */
	public function __construct(
		Reader $reader,
		GetEntityNameQuery $entityNameQuery
	) {
		$this->reader = $reader;
		$this->entityNameQuery = $entityNameQuery;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName() {
		return 'getCmsEntitySlug';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$cmsEntity = $this->reader->getClassAnnotation(
			$classMetadata->getReflectionClass(),
			'Sentient\\Cms\\Data\\Metadata\\Annotation\\CmsEntity'
		);
		if(!empty($cmsEntity->slug)) {
			return $cmsEntity->slug;
		}
		return $this->entityNameQuery->getResult($classMetadata);
	}

}