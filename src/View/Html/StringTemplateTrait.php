<?php
/**
 * Copied from CakePHP
 */
namespace Layer\View\Html;

/**
 * Provides an interface for registering and inserting
 * content into simple logic-less string templates.
 *
 * Used by several helpers to provide simple flexible templates
 * for generating HTML and other content.
 */
trait StringTemplateTrait {

	/**
	 * List of attributes that can be made compact.
	 *
	 * @var array
	 */
	protected $_compactAttributes = array(
		'compact', 'checked', 'declare', 'readonly', 'disabled', 'selected',
		'defer', 'ismap', 'nohref', 'noshade', 'nowrap', 'multiple', 'noresize',
		'autoplay', 'controls', 'loop', 'muted', 'required', 'novalidate', 'formnovalidate'
	);

	/**
	 * The templates this instance holds.
	 *
	 * @var array
	 */
	protected $_templates = [
		'attribute' => '{{name}}="{{value}}"',
		'compactAttribute' => '{{name}}="{{value}}"',
	];

	/**
	 * Constructor.
	 *
	 * @param array $templates A set of templates to add.
	 */
	public function __construct(array $templates = null) {

		if ($templates) {
			$this->addTemplate($templates);
		}

	}

	/**
	 * Add one or more template strings.
	 *
	 * @param array $templates The templates to add.
	 * @return void
	 */
	public function addTemplate(array $templates) {

		$this->_templates = array_merge($this->_templates, $templates);

	}

	/**
	 * Get a template.
	 *
	 * @param string $name Leave null to get all templates, provide a name to get a single template.
	 * @return string|array|null Either the template(s) or null
	 */
	public function getTemplate($name = null) {

		if (!isset($this->_templates[$name])) {
			return null;
		}

		return $this->_templates[$name];

	}

	/**
	 * Remove the named template.
	 *
	 * @param string $name The template to remove.
	 * @return void
	 */
	public function removeTemplate($name) {

		unset($this->_templates[$name]);

	}

	/**
	 * Format a template string with $data
	 *
	 * @param string $name The template name.
	 * @param array $data The data to insert.
	 * @return string
	 */
	public function formatTemplate($name, $data, $attrs = []) {

		$template = $this->getTemplate($name);
		if ($template === null) {
			return '';
		}
		if (is_bool($attrs)) {
			$attrs = ['escape' => $attrs];
		}
		if (isset($attrs['escape'])) {
			$escape = is_array($attrs['escape']) ? $attrs['escape'] : !!$attrs['escape'];
			unset($attrs['escape']);
		} else {
			$escape = false;
		}
		if (is_string($data)) {
			$data = ['content' => $data];
		}
		$data['attrs'] = $attrs;
		$replace = [];
		$keys = array_keys($data);
		foreach ($keys as $key) {
			$value = is_array($data[$key]) ? $this->formatTemplateAttributes($data[$key]) : $data[$key];
			if ($escape && (!is_array($escape) || in_array($key, $escape))) {
				$value = htmlspecialchars($value);
			}
			$replace['{{' . $key . '}}'] = $value;
		}

		return strtr($template, $replace);

	}

	/**
	 * Returns a space-delimited string with items of the $options array. If a key
	 * of $options array happens to be one of those listed
	 * in `StringTemplate::$_compactAttributes` and its value is one of:
	 *
	 * - '1' (string)
	 * - 1 (integer)
	 * - true (boolean)
	 * - 'true' (string)
	 *
	 * Then the value will be reset to be identical with key's name.
	 * If the value is not one of these 4, the parameter is not output.
	 *
	 * 'escape' is a special option in that it controls the conversion of
	 * attributes to their html-entity encoded equivalents. Set to false to disable html-encoding.
	 *
	 * If value for any option key is set to `null` or `false`, that option will be excluded from output.
	 *
	 * This method uses the 'attribute' and 'compactAttribute' templates. Each of
	 * these templates uses the `name` and `value` variables. You can modify these
	 * templates to change how attributes are formatted.
	 *
	 * @param array $options Array of options.
	 * @param null|array $exclude Array of options to be excluded, the options here will not be part of the return.
	 * @return string Composed attributes.
	 */
	public function formatTemplateAttributes($options, $exclude = null) {

		$insertBefore = ' ';
		$options = (array)$options + ['escape' => true];

		if (!is_array($exclude)) {
			$exclude = [];
		}

		$exclude = ['escape' => true, 'idPrefix' => true] + array_flip($exclude);
		$escape = $options['escape'];
		$attributes = [];

		foreach ($options as $key => $value) {
			if (!isset($exclude[$key]) && $value !== false && $value !== null) {
				$attributes[] = $this->_formatTemplateAttribute($key, $value, $escape);
			}
		}
		$out = trim(implode(' ', $attributes));

		return $out ? $insertBefore . $out : '';

	}

	/**
	 * Formats an individual attribute, and returns the string value of the composed attribute.
	 * Works with minimized attributes that have the same value as their name such as 'disabled' and 'checked'
	 *
	 * @param string $key The name of the attribute to create
	 * @param string $value The value of the attribute to create.
	 * @param boolean $escape Define if the value must be escaped
	 * @return string The composed attribute.
	 */
	protected function _formatTemplateAttribute($key, $value, $escape = true) {

		if (is_array($value)) {
			$value = implode(' ', $value);
		}
		if (is_numeric($key)) {
			return $this->formatTemplate('compactAttribute', [
				'name' => $value,
				'value' => $value
			]);
		}
		$truthy = [1, '1', true, 'true', $key];
		$isMinimized = in_array($key, $this->_compactAttributes);
		if ($isMinimized && in_array($value, $truthy, true)) {
			return $this->formatTemplate('compactAttribute', [
				'name' => $key,
				'value' => $key
			]);
		}
		if ($isMinimized) {
			return '';
		}

		return $this->formatTemplate('attribute', [
			'name' => $key,
			// @todo This isn't so good, should use $app->escape() somehow
			'value' => $escape ? $this->_escape($value) : $value
		]);

	}

	/**
	 * @param $value
	 * @return string
	 */
	protected function _escape($value) {

		return htmlspecialchars($value);
	}

}
