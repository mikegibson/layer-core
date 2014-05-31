<?php

namespace Layer\Cms\Node;

use Layer\Cms\Data\CmsRepositoryInterface;

interface RepositoryCmsNodeFactoryInterface {

	public function getRepositoryCmsNodes(CmsRepositoryInterface $repository);

}