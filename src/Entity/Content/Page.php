<?php

namespace Layer\Entity\Content;

/**
 * Layer\Page\Page
 *
 * @Entity
 * @Table(name="content_pages")
 */
class Page {

	/**
	 * @var int $id
	 *
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @var string $title
	 *
	 * @Column(type="string")
	 */
	protected $title;

	/**
	 * @var text $content
	 *
	 * @Column(type="text")
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