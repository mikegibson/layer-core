<?php

namespace Layer\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;
use Layer\Data\Metadata\QueryInterface;

class GetVisiblePropertyLabelsQuery implements QueryInterface {

	protected $getVisiblePropertiesQuery;

	protected $getPropertyLabelQuery;

	public function __construct(
		GetVisiblePropertiesQuery $getVisiblePropertiesQuery,
		GetPropertyLabelQuery $getPropertyLabelQuery
	) {
		$this->getVisiblePropertiesQuery = $getVisiblePropertiesQuery;
		$this->getPropertyLabelQuery = $getPropertyLabelQuery;
	}

	public function getName() {
		return 'getVisiblePropertyLabels';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		$important = !empty($options['important']);
		$labels = [];
		foreach($this->getVisiblePropertiesQuery->getResult($classMetadata, compact('important')) as $property) {
			$labels[$property] = $this->getPropertyLabelQuery->getResult($classMetadata, compact('property'));
		}
		return $labels;
	}

}