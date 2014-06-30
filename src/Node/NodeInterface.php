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
	public function getParentNode();

	/**
	 * @return NodeInterface[]
	 */
	public function getChildNodes();

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
	public function hasChildNode($name);

	/**
	 * @param $name
	 * @return NodeInterface
	 */
	public function getChildNode($name);

	/**
	 * @return NodeInterface
	 */
	public function getRootNode();

	/**
	 * @param NodeInterface $childNode
	 */
	public function registerChildNode(NodeInterface $childNode);

	/**
	 * @param NodeInterface $baseNode
	 * @param null $name
	 * @param null $label
	 * @param bool $baseChildrenAccessible
	 * @param bool $overwrite
	 * @param bool $prepend
	 * @return NodeInterface
	 */
	public function wrapChildNode(
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
	public function adoptChildNodes(NodeInterface $node);

	/**
	 * @param callable $callback
	 */
	public function sortChildNodes($callback);

}