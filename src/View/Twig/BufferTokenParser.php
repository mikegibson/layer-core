<?php

namespace Layer\View\Twig;

class BufferTokenParser extends \Twig_TokenParser {

	/**
	 * @var BufferExtension
	 */
	private $extension;

	private $baseTag;

	/**
	 * @param BufferExtension $extension
	 */
	public function __construct(BufferExtension $extension, $baseTag) {
		$this->extension = $extension;
		$this->baseTag = $baseTag;
	}

	public function getTag() {
		return $this->baseTag . 'buffer';
	}

	public function parse(\Twig_Token $token) {
		$parser = $this->parser;
		$stream = $parser->getStream();
		$stream->expect(\Twig_Token::BLOCK_END_TYPE);
		$body = $parser->subparse(array($this, 'testEndTag'), true);
		$stream->expect(\Twig_Token::BLOCK_END_TYPE);
		$this->extension->buffer($this->baseTag, $body->getAttribute('data'));
	}

	public function testEndTag(\Twig_Token $token) {
		return $token->test(['end' . $this->baseTag . 'buffer']);
	}

}