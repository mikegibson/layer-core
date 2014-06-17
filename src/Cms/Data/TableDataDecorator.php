<?php

namespace Layer\Cms\Data;

use Layer\Data\Paginator\TableDataDecoratorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TableDataDecorator implements TableDataDecoratorInterface {

	/**
	 * @var CmsRepositoryInterface
	 */
	private $repository;

	/**
	 * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @param CmsRepositoryInterface $repository
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(CmsRepositoryInterface $repository, UrlGeneratorInterface $urlGenerator) {
		$this->repository = $repository;
		$this->urlGenerator = $urlGenerator;
	}

	/**
	 * @param $value
	 * @param $key
	 * @param $object
	 * @return string
	 */
	public function decorate($value, $key, $object) {
		$isHtml = false;
		if(is_array($value)) {
			$value = '[ARRAY]';
		} elseif(is_object($value)) {
			if(method_exists($value, '__toString')) {
				$value = (string) $value;
			} elseif($value instanceof \DateTime) {
				$value = $this->formatDateTime($value);
			} else {
				$value = sprintf('[OBJECT:%s]', get_class($value));
			}
		}
		if(!$isHtml) {
			$value = $this->escape($value);
		}
		if($this->repository->queryMetadata('isTitleProperty', ['property' => $key])) {
			$value = $this->wrapLink($value, $object);
		}
		return $value;
	}

	protected function formatDateTime(\DateTime $dateTime) {
		return $dateTime->format('Y-m-d H:i:s');
	}

	protected function escape($value) {
		return htmlspecialchars($value);
	}

	protected function wrapLink($value, $object) {
		if(!$this->repository->hasCmsNode('edit')) {
			return $value;
		}
		$node = $this->repository->getCmsNode('edit');
		$url = $this->urlGenerator->generate('cms', ['node' => $node->getPath(), 'id' => $object->getId()]);
		return sprintf('<a href="%s">%s</a>', $this->escape($url), $value);
	}

}