<?php

namespace Sentient\Node;

interface NodeInterface {

	const SEPARATOR = '/';

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getLabel();

	/**
	 * @return null|NodeInterface
	 */
	public function getParent();

	/**
	 * @return NodeInterface[]
	 */
	public function getChildren();

	/**
	 * @return string
	 */
	public function getPath();

	/**
	 * @param $path
	 * @return NodeInterface
	 */
	public function getDescendent($path);

	/**
	 * @param $name
	 * @return bool
	 */
	public function hasChild($name);

	/**
	 * @param $name
	 * @return NodeInterface
	 */
	public function getChild($name);

	/**
	 * @return NodeInterface
	 */
	public function getRoot();

	/**
	 * @param NodeInterface $childNode
	 */
	public function registerChild(NodeInterface $childNode);

	/**
	 * @param NodeInterface $baseNode
	 * @param null $name
	 * @param null $label
	 * @param bool $baseChildrenAccessible
	 * @param bool $overwrite
	 * @param bool $prepend
	 * @return NodeInterface
	 */
	public function wrapChild(
		NodeInterface $baseNode,
		$name = null,
		$label = null,
		$baseChildrenAccessible = true,
		$overwrite = false,
		$prepend = false
	);

	/**
	 * @param NodeInterface $node
	 */
	public function adoptChildren(NodeInterface $node);

	/**
	 * @param callable $callback
	 */
	public function sortChildren($callback);

}