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
	 * @var text $content
	 *
	 * @ORM\Column(type="text")
	 * @Layer\HtmlContent
	 */
	protected $content;

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
	 * @return text
	 */
	public function getContent() {
		return $this->content;
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

}