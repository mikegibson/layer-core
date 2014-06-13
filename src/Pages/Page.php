<?php

namespace Layer\Pages;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Layer\Data\Metadata\Annotation as Layer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Layer\Pages\Page
 *
 * @ORM\Entity
 * @ORM\Table(name="content_pages")
 * @Behavior\Tree(type="nested")
 * @Layer\CrudEntity
 * @Layer\EntityName("pages")
 */
class Page {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @Layer\PropertyLabel("ID")
	 */
	protected $id;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 */
	protected $title;

	/**
	 * @var string $slug
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Behavior\Slug(fields={"title"})
	 */
	protected $slug;

	/**
	 * @var string $content
	 *
	 * @ORM\Column(type="text")
	 * @Layer\HtmlContent
	 */
	protected $content;

	/**
	 * @Behavior\TreeParent
	 * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $parent;

	/**
	 * @var int $lft
	 *
	 * @Behavior\TreeLeft
	 * @ORM\Column(type="integer")
	 */
	protected $lft;

	/**
	 * @var int $rght
	 *
	 * @Behavior\TreeRight
	 * @ORM\Column(type="integer")
	 */
	protected $rgt;

	/**
	 * @var int|null $root
	 *
	 * @Behavior\TreeRoot
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $root;

	/**
	 * @var int $level
	 *
	 * @Behavior\TreeLevel
	 * @ORM\Column(name="lvl", type="integer")
	 */
	protected $level;

	/**
	 * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
	 * @Layer\InvisibleProperty
	 */
	protected $children;

	/**
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="create")
	 * @Layer\InvisibleProperty
	 */
	protected $created;

	/**
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="update")
	 * @Layer\InvisibleProperty
	 */
	protected $updated;

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @return Page|null
	 */
	public function getParent() {
		return $this->parent;
	}

	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return int
	 */
	public function getLeft() {
		return $this->lft;
	}

	/**
	 * @return int
	 */
	public function getRight() {
		return $this->rgt;
	}

	/**
	 * @return int
	 */
	public function getRoot() {
		return $this->root;
	}

	/**
	 * @return int
	 */
	public function getLevel() {
		return $this->level;
	}

	public function getCreated() {
		return $this->created;
	}

	public function getUpdated() {
		return $this->updated;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function setSlug($slug) {
		$this->slug = $slug;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function setParent($parent) {
		$this->parent = $parent;
	}

	public function __toString() {
		return $this->getTitle();
	}

}