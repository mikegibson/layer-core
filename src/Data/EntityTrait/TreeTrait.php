<?php

namespace Layer\Data\EntityTrait;

use Gedmo\Mapping\Annotation as Behavior;

trait TreeTrait {

	/**
	 * @Behavior\TreeLeft
	 * @ORM\Column(type="integer")
	 */
	protected $lft;

	/**
	 * @Behavior\TreeRight
	 * @ORM\Column(type="integer")
	 */
	protected $rgt;

	/**
	 * @Behavior\TreeRoot
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $root;

	/**
	 * @Behavior\TreeLevel
	 * @ORM\Column(name="lvl", type="integer")
	 */
	protected $level;


	public function setParent($parent) {
		$this->parent = $parent;
	}

	public function getParent() {
		return $this->parent;
	}

	public function getRoot() {
		return $this->root;
	}

	public function getLevel() {
		return $this->level;
	}

	public function getChildren() {
		return $this->children;
	}

	public function getLeft() {
		return $this->lft;
	}

	public function getRight() {
		return $this->rgt;
	}
}