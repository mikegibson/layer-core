<?php

namespace Layer\View\Table;

use Layer\Application;
use Layer\Route\UrlGeneratorTrait;
use Layer\View\Html\StringTemplateTrait;

/**
 * Class TableHelper
 *
 * @package Layer\View\Table
 */
class TableHelper implements TableHelperInterface {

    use StringTemplateTrait, UrlGeneratorTrait;

    /**
     * @var \Silex\Application
     */
    protected $app;

    /**
     * Default templates the helper uses
     *
     * @var array
     */
    protected $_defaultTemplates = [
        'table' => '<table{{attrs}}>{{content}}</table>',
        'thead' => '<thead{{attrs}}>{{content}}</thead>',
        'tbody' => '<tbody{{attrs}}>{{content}}</tbody>',
        'tfoot' => '<tfoot{{attrs}}>{{content}}</tfoot>',
        'tr' => '<tr{{attrs}}>{{content}}</tr>',
        'th' => '<th{{attrs}}>{{content}}</th>',
        'td' => '<td{{attrs}}>{{content}}</td>',
        'link' => '<a{{attrs}}>{{content}}</a>',
        'class' => '{{tag}}-{{key}}'
    ];

    /**
     * @var array
     */
    protected $_defaultCellParams = [
        'value' => '',
        'escape' => false
    ];

    /**
     * Constructor
     */
    public function __construct(Application $app) {
        $this->app = $app;
        $this->addTemplate($this->_defaultTemplates);
    }

    /**
     * @param TableDataInterface $data
     * @param array $attrs
     * @return string
     */
    public function render(TableDataInterface $data, array $attrs = []) {
        return $this->wrap($this->thead($data) . $this->tbody($data), $attrs);
    }

    /**
     * @param $inner
     * @param array $attrs
     * @return string
     */
    public function wrap($inner, array $attrs = []) {
        return $this->formatTemplate('table', $inner, $attrs);
    }

    /**
     * @param TableDataInterface $data
     * @param array $attrs
     * @return string
     */
    public function thead(TableDataInterface $data, array $attrs = []) {

        return $this->formatTemplate('thead', $this->headerRow($data), $attrs);
    }

    /**
     * @param TableDataInterface $data
     * @param array $attrs
     * @return string
     */
    public function headerRow(TableDataInterface $data, array $attrs = []) {
        return $this->formatTemplate('tr', $this->headerColumns($data), $attrs);
    }

    /**
     * @param TableDataInterface $data
     * @param array $attrs
     * @return string
     */
    public function headerColumns(TableDataInterface $data, array $attrs = []) {

        $out = '';
        foreach ($data->getColumns() as $key => $label) {
            if (is_int($key)) {
                $key = null;
            }
            $out .= $this->headerCell($label, $attrs, $key);
        }

        return $out;
    }

    /**
     * @param TableDataInterface $data
     * @param array $attrs
     * @return string
     */
    public function tbody(TableDataInterface $data, array $attrs = []) {
        return $this->formatTemplate('tbody', $this->bodyRows($data), $attrs);
    }

    /**
     * @param TableDataInterface $data
     * @param array $rowAttrs
     * @return string
     */
    public function bodyRows(TableDataInterface $data, array $rowAttrs = [], $cellAttrs = []) {

        $out = '';
        $columnKeys = array_keys($data->getColumns());
        foreach ($data->getData() as $rowKey => $row) {
            if (is_int($rowKey)) {
                $rowKey = null;
            }
            $out .= $this->row($row, $columnKeys, $rowAttrs, $cellAttrs, $rowKey);
        }

        return $out;
    }

    /**
     * @param $row
     * @param array $columnKeys
     * @param array $rowAttrs
     * @param array $cellAttrs
     * @param null $rowKey
     * @return string
     */
    public function row($row, array $columnKeys = null, array $rowAttrs = [], array $cellAttrs = [], $rowKey = null) {

        if ($columnKeys === null) {
            $columnKeys = array_keys($row);
        }
        if ($rowKey && !isset($rowAttrs['class']) && $this->getTemplate('class')) {
            $rowAttrs['class'] = $this->formatTemplate('class', ['tag' => 'tr', 'key' => $rowKey]);
        }
        $inner = '';
        foreach ($columnKeys as $columnKey) {
            $value = isset($row[$columnKey]) ? (string)$row[$columnKey] : '';
            $inner .= $this->cell($value, $cellAttrs, $columnKey);
        }

        return $this->formatTemplate('tr', $inner, $rowAttrs);
    }

    /**
     * @param $label
     * @param array $attrs
     * @param null $key
     * @return string
     */
    public function headerCell($label, array $attrs = [], $key = null) {
        return $this->cell($label, $attrs, $key, 'th');
    }

    /**
     * @param $content
     * @param array $attrs
     * @param null $key
     * @param string $tag
     * @return mixed|string
     */
    public function cell($content, array $attrs = [], $key = null, $tag = 'td') {

        if (!is_array($content)) {
            $content = ['value' => $content];
        }
        $content = array_merge($this->_defaultCellParams, $content);
        $value = $content['value'];
        if ($key && !isset($attrs['class']) && $this->getTemplate('class')) {
            $attrs['class'] = $this->formatTemplate('class', compact('tag', 'key'));
        }
        if (!isset($content['escape']) || $content['escape']) {
            $content['value'] = $this->_escape($content['value']);
        }
        if (isset($content['url'])) {
            $args = (array)$content['url'];
            array_unshift($args, $this->app);
            $content['value'] = $this->formatTemplate('link', [
                'content' => $content['value'],
                'href' => call_user_func_array([$this, 'generateUrl'], $args)
            ]);
        }

        return $this->formatTemplate($tag, ['content' => $value], $attrs);
    }

    /**
     * @param $value
     * @return string
     */
    protected function _escape($value) {
        return $this->app->escape($value);
    }

}