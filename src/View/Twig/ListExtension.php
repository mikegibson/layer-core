<?php

namespace Layer\View\Twig;

use Layer\Node\ListNodeInterface;

class ListExtension extends TemplateBlockFunctionExtension {

	protected $template = 'block/list';

	protected $functionBlocks = [
		'list' => [
			'args' => ['node']
		],
		'list_item' => [
			'args' => ['node']
		],
		'list_item_value' => [
			'args' => ['node']
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