<?php

namespace Layer\View\Twig;

class BufferExtension extends \Twig_Extension {

	/**
	 * @var string[]
	 */
	private $baseTags = [];

	/**
	 * @var \Twig_Node_Text[]
	 */
	private $buffered = [];

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @param string[] $baseTags
	 */
	public function __construct(array $baseTags = []) {
		foreach($baseTags as $tag) {
			$this->addBaseTag($tag);
		}
	}

	/**
	 * @param $tag
	 * @throws \InvalidArgumentException
	 */
	public function addBaseTag($tag) {
		if(in_array($tag, $this->baseTags)) {
			throw new \InvalidArgumentException(sprintf('Base tag %s is already registered.', $tag));
		}
		$this->buffered[$tag] = [];
		$this->baseTags[] = $tag;
	}

	/**
	 * @param \Twig_Environment $environment
	 */
	public function initRuntime(\Twig_Environment $environment) {
		$this->twig = $environment;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return 'buffer';
	}

	/**
	 * @return BufferTokenParser[]
	 */
	public function getTokenParsers() {
		$parsers = [];
		foreach($this->baseTags as $tag) {
			$parsers[] = new BufferTokenParser($this, $tag);
		}
		return $parsers;
	}

	/**
	 * @return array
	 */
	public function getFunctions() {
		$functions = [];
		foreach($this->baseTags as $tag) {
			$functions[$tag . 'buffer_flush'] = new \Twig_Function_Function(function() use($tag) {
				$buffered = $this->buffered[$tag];
				$this->buffered[$tag] = [];
				return implode("\n", $buffered);
			}, ['is_safe' => ['html']]);
		}
		return $functions;
	}

	/**
	 * @param string $tag
	 * @param string $content
	 */
	public function buffer($tag, $content) {
		$this->buffered[$tag][] = $content;
	}

}