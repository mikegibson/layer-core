<?php

namespace Layer\Node;

class RootNode extends OrphanNode {

	private $name;

	private $label;

	public function __construct($name = 'root', $label = 'Root') {
		$this->name = $name;
		$this->label = $label;
	}

	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

}