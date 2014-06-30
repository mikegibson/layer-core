<?php

namespace Sentient\Pages;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Sentient\Cms\Data\Metadata\Annotation as Cms;
use Sentient\Data\Metadata\Annotation as Sentient;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sentient\Pages\Page
 *
 * @ORM\Entity
 * @ORM\Table(name="content_pages")
 * @Behavior\Tree(type="nested")
 * @Sentient\CrudEntity
 * @Sentient\EntityName("pages")
 * @Cms\RootNodePath("content/pages")
 */
class Page {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @Sentient\PropertyLabel("ID")
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
	 * @Sentient\HtmlProperty
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
	 * @Sentient\InvisibleProperty
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
	 * @Sentient\InvisibleProperty
	 */
	protected $children;

	/**
	 * @var \DateTime $created
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="create")
	 * @Sentient\InvisibleProperty
	 */
	protected $created;

	/**
	 * @var \DateTime $updated
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="update")
	 * @Sentient\InvisibleProperty
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
	 * @Sentient\InvisibleProperty
	 */
	public function getLeft() {
		return $this->lft;
	}

	/**
	 * @return int
	 * @Sentient\InvisibleProperty
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

	/**
	 * @return \DateTime
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return \DateTime
	 */
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