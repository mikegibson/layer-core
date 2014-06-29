<?php

namespace Layer\View\Twig;

class FlashExtension extends TemplateBlockFunctionExtension {

	public $template = 'block/flash';

	public $functionBlocks = [
		'flash' => [
			'args' => ['key', 'theme']
		],
		'flash_container' => [
			'args' => ['messages', 'key', 'theme']
		],
		'flash_messages' => [
			'args' => ['key', 'theme']
		],
		'flash_message' => [
			'args' => ['key', 'message', 'theme']
		]
	];

	public function getName() {
		return 'flash';
	}

}