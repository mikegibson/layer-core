<?php

namespace Sentient\View\Twig;

/**
 * Token Parser for template block theme tags.
 */
class TemplateBlockThemeTokenParser extends \Twig_TokenParser {

	/**
	 * @var TemplateBlockFunctionExtension
	 */
	protected $extension;

	/**
	 * @param TemplateBlockFunctionExtension $extension
	 */
	public function __construct(TemplateBlockFunctionExtension $extension) {
		$this->extension = $extension;
	}

	/**
	 * @param \Twig_Token $token
	 * @return TemplateBlockThemeNode|\Twig_NodeInterface
	 */
	public function parse(\Twig_Token $token) {
		$theme = $this->parser->getExpressionParser()->parseExpression();
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
		$node = new TemplateBlockThemeNode(compact('theme'), [], $token->getLine(), $this->getTag());
		$node->setExtensionName($this->extension->getName());
		return $node;
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag() {
		return $this->extension->getName() . '_theme';
	}

}
