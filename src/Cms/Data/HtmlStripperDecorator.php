<?php

namespace Sentient\Cms\Data;

use Sentient\Data\RepositoryManagerInterface;
use Sentient\Data\TableData\TableDataDecoratorInterface;

class HtmlStripperDecorator implements TableDataDecoratorInterface {

	private $repositoryManager;

	public function __construct(RepositoryManagerInterface $repositoryManager) {
		$this->repositoryManager = $repositoryManager;
	}

	public function decorateColumns(array $columns) {
		return $columns;
	}

	public function decorateData($value, $key, $object) {
		try {
			$repository = $this->repositoryManager->getRepositoryForEntity($object);
		} catch(\InvalidArgumentException $e) {
			return $value;
		}
		if($repository->queryMetadata('isHtmlProperty', ['property' => $key])) {
			$value = strip_tags($value);
		}
		return $value;
	}

}