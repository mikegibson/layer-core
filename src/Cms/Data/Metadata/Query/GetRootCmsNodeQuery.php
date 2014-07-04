<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class GetRootCmsNodeQuery extends CmsNodeQuery {

	public function getName() {
		return 'getRootCmsNode';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(!$repository = $this->getRepository($classMetadata)) {
			return false;
		}
		try {
			return $this->getNodeRegistry()->getRoot($repository);
		} catch(\InvalidArgumentException $e) {
			return false;
		}
	}

}