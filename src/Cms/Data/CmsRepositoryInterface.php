<?php

namespace Layer\Cms\Data;

use Layer\Data\ManagedRepositoryInterface;
use Layer\Node\ControllerNodeInterface;

interface CmsRepositoryInterface extends ManagedRepositoryInterface {

	public function getCmsSlug();

	public function getRootCmsNode();

	public function hasCmsNode($name);

	public function getCmsNode($name);

	public function registerCmsNode(ControllerNodeInterface $node, $isRootNode = false);

}