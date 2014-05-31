<?php

namespace Layer\Node;

class RootNode extends OrphanNode {

	private $key;

	private $label;

	public function __construct($key = 'root', $label = 'Root') {
		$this->key = $key;
		$this->label = $label;
	}

	public function getName() {
		return $this->key;
	}

	public function getLabel() {
		return $this->label;
	}

}