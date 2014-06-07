<?php

namespace Layer\Node;

interface NodeInterface {

	const SEPARATOR = '/';

	public function getName();

	public function getLabel();

	public function getParentNode();

	public function getChildNodes();

	public function getPath();

	public function getDescendent($path);

	public function hasChildNode($name);

	public function getChildNode($name);

	public function getRootNode();

	public function registerChildNode(NodeInterface $childNode);

	public function wrapChildNode(NodeInterface $baseNode);

	public function sortChildNodes($callback);

}