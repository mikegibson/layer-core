<?php

namespace Layer\View\Twig;

class FlashExtension extends TemplateBlockFunctionExtension {

	public $template = 'block/flash';

	public $functionBlocks = [
		'flash' => [
			'args' => ['key']
		],
		'flash_container' => [
			'args' => ['messages', 'key']
		],
		'flash_messages' => [
			'args' => ['key']
		],
		'flash_message' => [
			'args' => ['key', 'message']
		]
	];

	public function getName() {
		return 'flash';
	}

}