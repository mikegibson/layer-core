<?php

namespace Sentient\Cms\Node;

use Sentient\Cms\Data\CmsRepositoryInterface;

interface RepositoryCmsNodeFactoryInterface {

	public function getRepositoryCmsNodes(CmsRepositoryInterface $repository);

}