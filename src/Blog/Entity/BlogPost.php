<?php

namespace Sentient\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Sentient\Cms\Data\Metadata\Annotation as Cms;
use Sentient\Data\Metadata\Annotation as Sentient;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_posts")
 * @Sentient\CrudEntity
 * @Sentient\EntityName("blog_posts")
 * @Cms\RootNodePath("content/blog")
 */
class BlogPost {

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
	 * @var string $introduction
	 *
	 * @ORM\Column(type="text")
	 * @Sentient\HtmlProperty
	 */
	protected $introduction;

	/**
	 * @var string $content
	 *
	 * @ORM\Column(type="text")
	 * @Sentient\HtmlProperty
	 */
	protected $content;

	/**
	 * @var BlogCategory $category
	 *
	 * @ORM\ManyToOne(targetEntity="BlogCategory")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
	 */
	protected $category;

	/**
	 * @var \DateTime $postDateTime
	 *
	 * @ORM\Column(type="datetime", nullable=false)
	 */
	protected $publishedDate;

	/**
	 * @var \DateTime $created
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="create")
	 */
	protected $created;

	/**
	 * @var \DateTime $updated
	 *
	 * @ORM\Column(type="datetime")
	 * @Behavior\Timestampable(on="update")
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
	public function getIntroduction() {
		return $this->introduction;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @return BlogCategory
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * @return \DateTime
	 */
	public function getPublishedDate() {
		return $this->publishedDate;
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

	public function setIntroduction($introduction) {
		$this->introduction = $introduction;
	}

	public function setContent($content) {
		$this->content = $content;
	}

	public function setCategory(BlogCategory $category) {
		$this->category = $category;
	}

	public function setPublishedDate(\DateTime $publishedDate) {
		$this->publishedDate = $publishedDate;
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function preUpdate() {
		if(!$this->getPublishedDate()) {
			$this->setPublishedDate($this->getCreated() ?: new \DateTime());
		}
	}

	public function __toString() {
		return $this->getTitle();
	}

}