<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class GetCmsNodeQuery extends CmsNodeQuery {

	public function getName() {
		return 'getCmsNode';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(empty($options['action'])) {
			throw new \InvalidArgumentException('No action was specified.');
		}
		if(!$repository = $this->getRepository($classMetadata)) {
			return false;
		}
		try {
			return $this->getNodeRegistry()->get($repository, $options['action']);
		} catch(\InvalidArgumentException $e) {
			return false;
		}
	}

}