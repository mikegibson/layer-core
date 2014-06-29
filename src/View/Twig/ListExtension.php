<?php

namespace Layer\View\Twig;

use Layer\Node\ListNodeInterface;

class ListExtension extends TemplateBlockFunctionExtension {

	protected $template = 'block/list';

	protected $functionBlocks = [
		'list' => [
			'args' => ['node', 'theme']
		],
		'list_item' => [
			'args' => ['node', 'theme']
		],
		'list_item_value' => [
			'args' => ['node', 'theme']
		]
	];

	public function getName() {
		return 'list';
	}

	public function beforeRender($block, array $context) {
		if(!isset($context['node']) || !($context['node'] instanceof ListNodeInterface)) {
			throw new \InvalidArgumentException('Lists must implement ListNodeInterface.');
		}
		return parent::beforeRender($block, $context);
	}

}