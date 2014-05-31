<?php

namespace Layer\Node;

interface NodeInterface {

	const SEPARATOR = '/';

	public function getKey();

	public function getLabel();

	public function getParentNode();

	public function getChildNodes();

	public function getPath();

	public function getDescendent($path);

	public function hasChildNode($key);

	public function getChildNode($key);

	public function registerChildNode(NodeInterface $childNode);

	public function wrapChildNode(NodeInterface $baseNode);

	public function sortChildNodes($callback);

}