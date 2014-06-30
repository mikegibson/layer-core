<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sentient\Data\Metadata\QueryInterface;
use Sentient\Utility\InflectorInterface;

/**
 * Class GetCmsEntitySlugQuery
 * @package Sentient\Cms\Data\Metadata\Query
 */
class GetCmsEntitySlugQuery implements QueryInterface {

	/**
	 * @var GetCmsEntityQuery
	 */
	protected $getCmsEntityQuery;

	/**
	 * @var \Sentient\Utility\InflectorInterface
	 */
	protected $inflector;

	/**
	 * @param Reader $reader
	 * @param InflectorInterface $inflector
	 */
	public function __construct(GetCmsEntityQuery $getCmsEntityQuery, InflectorInterface $inflector) {
		$this->getCmsEntityQuery = $getCmsEntityQuery;
		$this->inflector = $inflector;
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
		$annotation = $this->getCmsEntityQuery->getResult($classMetadata);
		if(!empty($annotation->slug)) {
			return $annotation->slug;
		}
		return $this->getFallbackResult($classMetadata, $options);
	}

	/**
	 * @param ClassMetadata $classMetadata
	 * @param array $options
	 * @return mixed
	 */
	protected function getFallbackResult(ClassMetadata $classMetadata, array $options = []) {
		$replacement = isset($options['replacement']) ? $options['replacement'] : '-';
		$parts = explode('\\', $classMetadata->getName());
		$className = array_pop($parts);
		return str_replace('_', $replacement, $this->inflector->underscore($className));
	}

}