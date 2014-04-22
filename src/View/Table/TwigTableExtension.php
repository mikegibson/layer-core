<?php

namespace Layer\View\Table;

use Layer\Paginator\Paginator;
use Layer\Paginator\PaginatorResult;
use Layer\View\Twig\TemplateBlockFunctionExtension;

class TwigTableExtension extends TemplateBlockFunctionExtension {

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
				$context['dataType'] = $dataType = $result->getDataType();
				if(isset($context['columnKey']) && $dataType->hasField($context['columnKey'])) {
					$context['field'] = $dataType->field($context['columnKey']);
				}
			}
		}
		return parent::beforeRender($block, $context);
	}

}