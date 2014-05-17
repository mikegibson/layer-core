<?php

namespace Layer\Pages;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * Layer\Page\Page
 *
 * @ORM\Entity
 * @ORM\Table(name="content_pages")
 */
class Page {

	/**
	 * @var int $id
	 *
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 */
	protected $id;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(type="string")
	 */
	protected $title;

	/**
	 * @var text $content
	 *
	 * @ORM\Column(type="text")
	 */
	protected $content;

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
	 * @return text
	 */
	public function getContent() {
		return $this->content;
	}
	public function setTitle($title) {
		$this->title = $title;
	}

	public function setContent($content) {
		$this->content = $content;
	}

}