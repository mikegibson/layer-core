<?php

namespace Layer\Cms\Data;

use Layer\Data\RepositoryManagerInterface;
use Layer\Data\TableData\TableDataDecoratorInterface;

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