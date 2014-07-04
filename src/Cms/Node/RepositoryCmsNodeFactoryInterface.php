<?php

namespace Sentient\Cms\Node;

use Sentient\Data\ManagedRepositoryInterface;

interface RepositoryCmsNodeFactoryInterface {

	public function createNodes(ManagedRepositoryInterface $repository);

}