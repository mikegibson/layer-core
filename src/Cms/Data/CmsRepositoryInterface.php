<?php

namespace Layer\Cms\Data;

use Layer\Data\ManagedRepositoryInterface;
use Layer\Node\ControllerNodeInterface;

interface CmsRepositoryInterface extends ManagedRepositoryInterface {

	/**
	 * @return string
	 */
	public function getCmsSlug();

	/**
	 * @return ControllerNodeInterface
	 */
	public function getRootCmsNode();

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasCmsNode($name);

	/**
	 * @param $name
	 * @return ControllerNodeInterface
	 * @throws \InvalidArgumentException
	 */
	public function getCmsNode($name);

	/**
	 * @param ControllerNodeInterface $node
	 * @param bool $isRootNode
	 */
	public function registerCmsNode(ControllerNodeInterface $node, $isRootNode = false);

}