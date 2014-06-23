<?php

namespace Layer\View\Twig;

use Layer\Data\Paginator\Paginator;
use Layer\Data\Paginator\PaginatorResult;
use Layer\Data\TableData\TableDataInterface;

class TableExtension extends TemplateBlockFunctionExtension {

	protected $template = 'block/table';

	protected $functionBlocks = [
		'table' => [
			'args' => ['table']
		],
		'table_thead' => [
			'args' => ['table']
		],
		'table_thead_rows' => [
			'args' => ['table']
		],
		'table_column_headers' => [
			'args' => ['table']
		],
		'table_column_label' => [
			'args' => ['table', 'label', 'columnKey']
		],
		'table_tbody' => [
			'args' => ['table']
		],
		'table_body_rows' => [
			'args' => ['table']
		],
		'table_body_row' => [
			'args' => ['table', 'row', 'rowKey']
		],
		'table_body_cell' => [
			'args' => ['table', 'value', 'columnKey', 'rowKey', 'row']
		],
		'table_value' => [
			'args' => ['table', 'value', 'columnKey', 'rowKey', 'row']
		],
		'table_tfoot' => [
			'args' => ['table']
		]
	];

	public function getName() {
		return 'table';
	}

	public function beforeRender($block, array $context) {
		if(!isset($context['table']) || !($context['table'] instanceof TableDataInterface)) {
			throw new \InvalidArgumentException('Tables must implement TableDataInterface');
		}
		if($context['table'] instanceof Paginator) {
			$result = $context['table']->getResult();
			if($result instanceof PaginatorResult) {
				$context['repository'] = $result->getRepository();
			}
		}
		return parent::beforeRender($block, $context);
	}

}