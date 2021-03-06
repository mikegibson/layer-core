<?php

namespace Sentient\Blog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Behavior;
use Sentient\Cms\Data\Metadata\Annotation as Cms;
use Sentient\Data\Metadata\Annotation as Sentient;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_categories")
 * @Sentient\CrudEntity
 * @Sentient\EntityName("blog_categories")
 * @Cms\RootNodePath("content/blog/categories")
 */
class BlogCategory {

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
	protected $name;

	/**
	 * @var string $slug
	 *
	 * @ORM\Column(type="string", unique=true)
	 * @Behavior\Slug(fields={"name"})
	 */
	protected $slug;

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
	public function getName() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
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

	public function setName($name) {
		$this->name = $name;
	}

	public function setSlug($slug) {
		$this->slug = $slug;
	}

	public function __toString() {
		return $this->getName();
	}

}