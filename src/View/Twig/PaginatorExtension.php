<?php

namespace Layer\View\Twig;

use Layer\Data\Paginator\PaginatorInterface;

class PaginatorExtension extends TemplateBlockFunctionExtension {

	protected $template = 'block/paginator';

	protected $functionBlocks = [
		'paginator_count' => [
			'args' => ['paginator']
		],
		'paginator_links' => [
			'args' => ['paginator']
		],
		'paginator_link' => [
			'args' => ['paginator', 'page', 'label']
		],
		'paginator_prev' => [
			'args' => ['paginator', 'label']
		],
		'paginator_next' => [
			'args' => ['paginator', 'label']
		],
		'paginator_first' => [
			'args' => ['paginator', 'label']
		],
		'paginator_last' => [
			'args' => ['paginator', 'label']
		]
	];

	public function getName() {
		return 'paginator';
	}

	public function beforeRender($block, array $context) {
		if(!isset($context['paginator']) || !($context['paginator'] instanceof PaginatorInterface)) {
			throw new \InvalidArgumentException('Paginators must implement PaginatorInterface');
		}
		return parent::beforeRender($block, $context);
	}

}