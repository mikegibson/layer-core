<?php

namespace Layer\Cms\Data;

use Layer\Data\RepositoryManagerInterface;
use Layer\Data\TableData\TableDataDecoratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkerDecorator implements TableDataDecoratorInterface {

	/**
	 * @var CmsRepositoryInterface
	 */
	private $repositoryManager;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @param RepositoryManagerInterface $repositoryManager
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(RepositoryManagerInterface $repositoryManager, UrlGeneratorInterface $urlGenerator) {
		$this->repositoryManager = $repositoryManager;
		$this->urlGenerator = $urlGenerator;
	}

	public function decorateColumns(array $columns) {
		return $columns;
	}

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return string
	 */
	public function decorateData($value, $key, $object) {
		try {
			$repository = $this->repositoryManager->getRepositoryForEntity($object);
		} catch(\InvalidArgumentException $e) {
			return $value;
		}
		if(
			$repository instanceof CmsRepositoryInterface &&
			$repository->queryMetadata('isTitleProperty', ['property' => $key]) &&
			$repository->hasCmsNode('edit')
		) {
			$node = $repository->getCmsNode('edit');
			$url = $this->urlGenerator->generate('cms', ['node' => $node->getPath(), 'id' => $object->getId()]);
			$value = sprintf('<a href="%s">%s</a>', htmlspecialchars($url), $value);
		}
		return $value;
	}

}