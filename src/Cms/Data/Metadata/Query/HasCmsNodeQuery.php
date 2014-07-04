<?php

namespace Sentient\Cms\Data\Metadata\Query;

use Doctrine\ORM\Mapping\ClassMetadata;

class HasCmsNodeQuery extends CmsNodeQuery {

	public function getName() {
		return 'hasCmsNode';
	}

	public function getResult(ClassMetadata $classMetadata, array $options = []) {
		if(empty($options['action'])) {
			throw new \InvalidArgumentException('No action was specified.');
		}
		if(!$repository = $this->getRepository($classMetadata)) {
			return false;
		}
		return $this->getNodeRegistry()->has($repository, $options['action']);
	}

}