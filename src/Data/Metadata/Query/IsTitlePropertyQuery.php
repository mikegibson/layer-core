<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class IsTitlePropertyQuery implements QueryInterface {

	/**
	 * @var GetPropertyAnnotationQuery
	 */
	private $annotationQuery;

	protected $annotationClass = 'Layer\\Data\\Metadata\\Annotation\\TitleProperty';

	protected $titleProperties = [
		'title', 'name'
	];

	/**
	 * @param GetPropertyAnnotationQuery $annotationQuery
	 */
	public function __construct(GetPropertyAnnotationQuery $annotationQuery) {
		$this->annotationQuery = $annotationQuery;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'isTitleProperty';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if($annotation = $this->annotationQuery->getResult($classMetadata, array_merge($options, [
			'annotationClass' => $this->annotationClass
		]))) {
			return true;
		}
		if(!empty($options['strict'])) {
			return false;
		}
		return in_array($options['property'], $this->titleProperties);
	}

}