<?php
/**
 * Copied from CakePHP
 */
namespace Layer\View\Html;

use Layer\Application;
use Layer\Route\UrlGeneratorTrait;
use Layer\View\Html\StringTemplateTrait;

/**
 * Html Helper class for easy use of HTML widgets.
 *
 * HtmlHelper encloses all methods needed while working with HTML pages.
 *
 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html
 */
class HtmlHelper {

	use StringTemplateTrait, UrlGeneratorTrait;

	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * Default templates the helper users.
	 *
	 * @var array
	 */
	protected $_defaultTemplates = [
		'meta' => '<meta{{attrs}}/>',
		'metalink' => '<link href="{{url}}"{{attrs}}/>',
		'link' => '<a href="{{url}}"{{attrs}}>{{content}}</a>',
		'mailto' => '<a href="mailto:{{url}}"{{attrs}}>{{content}}</a>',
		'image' => '<img src="{{url}}"{{attrs}}/>',
		'tableheader' => '<th{{attrs}}>{{content}}</th>',
		'tableheaderrow' => '<tr{{attrs}}>{{content}}</tr>',
		'tablecell' => '<td{{attrs}}>{{content}}</td>',
		'tablerow' => '<tr{{attrs}}>{{content}}</tr>',
		'block' => '<div{{attrs}}>{{content}}</div>',
		'blockstart' => '<div{{attrs}}>',
		'blockend' => '</div>',
		'tag' => '<{{tag}}{{attrs}}>{{content}}</{{tag}}>',
		'tagstart' => '<{{tag}}{{attrs}}>',
		'tagend' => '</{{tag}}>',
		'tagselfclosing' => '<{{tag}}{{attrs}}/>',
		'para' => '<p{{attrs}}>{{content}}</p>',
		'parastart' => '<p{{attrs}}>',
		'css' => '<link rel="{{rel}}" href="{{url}}"{{attrs}}/>',
		'style' => '<style{{attrs}}>{{content}}</style>',
		'charset' => '<meta http-equiv="Content-Type" content="text/html; charset={{charset}}" />',
		'ul' => '<ul{{attrs}}>{{content}}</ul>',
		'ol' => '<ol{{attrs}}>{{content}}</ol>',
		'li' => '<li{{attrs}}>{{content}}</li>',
		'javascriptblock' => '<script{{attrs}}>{{content}}</script>',
		'javascriptstart' => '<script>',
		'javascriptlink' => '<script src="{{url}}"{{attrs}}></script>',
		'javascriptend' => '</script>'
	];

	/**
	 * Names of script files that have been included once
	 *
	 * @var array
	 */
	protected $_includedScripts = array();

	/**
	 * Options for the currently opened script block buffer if any.
	 *
	 * @var array
	 */
	protected $_scriptBlockOptions = array();

	/**
	 * Document type definitions
	 *
	 * @var array
	 */
	protected $_docTypes = array(
		'html4-strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
		'html4-trans' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
		'html4-frame' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		'html5' => '<!DOCTYPE html>',
		'xhtml-strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		'xhtml-trans' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'xhtml-frame' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
		'xhtml11' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
	);

	/**
	 * Constructor
	 */
	public function __construct(Application $app) {

		$this->app = $app;

		$this->addTemplate($this->_defaultTemplates);

	}

	/**
	 * Returns a doctype string.
	 *
	 * Possible doctypes:
	 *
	 *  - html4-strict:  HTML4 Strict.
	 *  - html4-trans:  HTML4 Transitional.
	 *  - html4-frame:  HTML4 Frameset.
	 *  - html5: HTML5. Default value.
	 *  - xhtml-strict: XHTML1 Strict.
	 *  - xhtml-trans: XHTML1 Transitional.
	 *  - xhtml-frame: XHTML1 Frameset.
	 *  - xhtml11: XHTML1.1.
	 *
	 * @param string $type Doctype to use.
	 * @return string Doctype string
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::docType
	 */
	public function docType($type = 'html5') {

		if (isset($this->_docTypes[$type])) {
			return $this->_docTypes[$type];
		}

		return null;

	}

	/**
	 * Creates a link to an external resource and handles basic meta tags
	 *
	 * Create a meta tag that is output inline:
	 *
	 * `$this->Html->meta('icon', 'favicon.ico');
	 *
	 * @param string $type The title of the external resource
	 * @param string|array $url The address of the external resource or string for content attribute
	 * @param array $options Other attributes for the generated tag. If the type attribute is html,
	 *    rss, atom, or icon, the mime-type is returned.
	 * @return string A completed `<link />` element.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::meta
	 */
	public function meta($type, $url = null, array $options = []) {

		if (!is_array($type)) {
			$types = array(
				'rss' => array('type' => 'application/rss+xml', 'rel' => 'alternate', 'title' => $type, 'link' => $url),
				'atom' => array('type' => 'application/atom+xml', 'title' => $type, 'link' => $url),
				'icon' => array('type' => 'image/x-icon', 'rel' => 'icon', 'link' => $url),
				'keywords' => array('name' => 'keywords', 'content' => $url),
				'description' => array('name' => 'description', 'content' => $url),
			);

			if ($type === 'icon' && $url === null) {
				$types['icon']['link'] = 'favicon.ico';
			}

			if (isset($types[$type])) {
				$type = $types[$type];
			} elseif (!isset($options['type']) && $url !== null) {
				if (is_array($url) && isset($url['ext'])) {
					$type = $types[$url['ext']];
				} else {
					$type = $types['rss'];
				}
			} elseif (isset($options['type']) && isset($types[$options['type']])) {
				$type = $types[$options['type']];
				unset($options['type']);
			} else {
				$type = array();
			}
		}

		$options = array_merge($type, $options);
		$out = null;

		if (isset($options['link'])) {
			$options['link'] = $this->generateAssetUrl($options['link']);
			if (isset($options['rel']) && $options['rel'] === 'icon') {
				$out = $this->formatTemplate('metalink', [
					'url' => $options['link'],
					'attrs' => $this->formatTemplateAttributes($options, ['link'])
				]);
				$options['rel'] = 'shortcut icon';
			}
			$out .= $this->formatTemplate('metalink', [
				'url' => $options['link'],
				'attrs' => $this->formatTemplateAttributes($options, ['link'])
			]);
		} else {
			$out = $this->formatTemplate('meta', [
				'attrs' => $this->formatTemplateAttributes($options, ['type'])
			]);
		}

		return $out;

	}

	/**
	 * Returns a charset META-tag.
	 *
	 * @param string $charset The character set to be used in the meta tag. If empty,
	 *  The App.encoding value will be used. Example: "utf-8".
	 * @return string A meta tag containing the specified character set.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::charset
	 */
	public function charset($charset = null) {

		return $this->formatTemplate('charset', [
			'charset' => ($charset === null) ? 'utf-8' : $charset
		]);

	}

	/**
	 * Creates an HTML link.
	 *
	 * If $url starts with "http://" this is treated as an external link. Else,
	 * it is treated as a path to controller/action and parsed with the
	 * HtmlHelper::url() method.
	 *
	 * If the $url is empty, $title is used instead.
	 *
	 * ### Options
	 *
	 * - `escape` Set to false to disable escaping of title and attributes.
	 * - `escapeTitle` Set to false to disable escaping of title. (Takes precedence over value of `escape`)
	 * - `confirm` JavaScript confirmation message.
	 *
	 * @param string $title The content to be wrapped by <a> tags.
	 * @param string|array $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
	 * @param array $options Array of options and HTML attributes.
	 * @param string $confirmMessage JavaScript confirmation message.
	 * @return string An `<a />` element.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::link
	 */
	public function link($title, $url, array $parameters = [], $options = array(), $confirmMessage = false) {

		$url = $this->generateUrl($this->app, $url, $parameters);

		$escapeTitle = true;
		if (isset($options['escapeTitle'])) {
			$escapeTitle = $options['escapeTitle'];
			unset($options['escapeTitle']);
		} elseif (isset($options['escape'])) {
			$escapeTitle = $options['escape'];
		}

		if ($escapeTitle === true) {
			$title = $this->app->escape($title);
		} elseif (is_string($escapeTitle)) {
			$title = htmlentities($title, ENT_QUOTES, $escapeTitle);
		}

		if (!empty($options['confirm'])) {
			$confirmMessage = $options['confirm'];
			unset($options['confirm']);
		}
		if ($confirmMessage) {
			$options['onclick'] = $this->_confirm($confirmMessage, 'return true;', 'return false;', $options);
		} elseif (isset($options['default']) && !$options['default']) {
			if (isset($options['onclick'])) {
				$options['onclick'] .= ' ';
			} else {
				$options['onclick'] = '';
			}
			$options['onclick'] .= 'event.returnValue = false; return false;';
			unset($options['default']);
		}

		return $this->formatTemplate('link', [
			'url' => $url,
			'attrs' => $options,
			'content' => $title
		]);

	}

	/**
	 * Returns a string to be used as onclick handler for confirm dialogs.
	 *
	 * @param string $message Message to be displayed
	 * @param string $okCode Code to be executed after user chose 'OK'
	 * @param string $cancelCode Code to be executed after user chose 'Cancel'
	 * @param array $options Array of options
	 * @return string onclick JS code
	 * @todo Use a buffer instead of an onclick attribute
	 */
	protected function _confirm($message, $okCode, $cancelCode = '', $options = array()) {

		$message = json_encode($message);
		$confirm = "if (confirm({$message})) { {$okCode} } {$cancelCode}";
		if (isset($options['escape']) && $options['escape'] === false) {
			$confirm = $this->app->escape($confirm);
		}

		return $confirm;

	}

	/**
	 * Creates a link element for CSS stylesheets.
	 *
	 * ### Usage
	 *
	 * Include one CSS file:
	 *
	 * `echo $this->Html->css('styles.css');`
	 *
	 * Include multiple CSS files:
	 *
	 * `echo $this->Html->css(array('one.css', 'two.css'));`
	 *
	 * ### Options
	 *
	 * - `rel` Defaults to 'stylesheet'. If equal to 'import' the stylesheet will be imported.
	 * - `fullBase` If true the URL will get a full address for the css file.
	 *
	 * @param string|array $path The name of a CSS style sheet or an array containing names of
	 *   CSS stylesheets. If `$path` is prefixed with '/', the path will be relative to the webroot
	 *   of your application. Otherwise, the path will be relative to your CSS path, usually webroot/css.
	 * @param array $options Array of options and HTML arguments.
	 * @return string CSS <link /> or <style /> tag, depending on the type of link.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::css
	 */
	public function css($path, $options = []) {

		if (!is_array($options)) {
			$rel = $options;
			$options = [];
			if ($rel) {
				$options['rel'] = $rel;
			}
			if (func_num_args() > 2) {
				$options = func_get_arg(2) + $options;
			}
			unset($rel);
		}

		$options += ['rel' => 'stylesheet'];

		if (is_array($path)) {
			$out = '';
			foreach ($path as $i) {
				$out .= "\n\t" . $this->css($i, $options);
			}

			return $out . "\n";
		}

		if (strpos($path, '//') !== false) {
			$url = $path;
		} else {
			$url = $this->generateAssetUrl($path, $options + array('pathPrefix' => 'css', 'ext' => '.css'));
			$options = array_diff_key($options, array('fullBase' => null, 'pathPrefix' => null));
		}

		if ($options['rel'] === 'import') {
			$out = $this->formatTemplate('style', [
				'attrs' => $this->formatTemplateAttributes($options, ['rel']),
				'content' => '@import url(' . $url . ');',
			]);
		} else {
			$out = $this->formatTemplate('css', [
				'rel' => $options['rel'],
				'url' => $url,
				'attrs' => $this->formatTemplateAttributes($options, ['rel']),
			]);
		}

		return $out;

	}

	/**
	 * Returns one or many `<script>` tags depending on the number of scripts given.
	 *
	 * If the filename is prefixed with "/", the path will be relative to the base path of your
	 * application. Otherwise, the path will be relative to your JavaScript path, usually webroot/js.
	 *
	 *
	 * ### Usage
	 *
	 * Include one script file:
	 *
	 * `echo $this->Html->script('styles.js');`
	 *
	 * Include multiple script files:
	 *
	 * `echo $this->Html->script(array('one.js', 'two.js'));`
	 *
	 * ### Options
	 *
	 * - `fullBase` If true the url will get a full address for the script file.
	 *
	 * @param string|array $url String or array of javascript files to include
	 * @param array $options Array of options, and html attributes see above.
	 * @return mixed String of `<script />` tags or null if block is specified in options
	 *   or if $once is true and the file has been included before.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::script
	 */
	public function script($url, $options = array()) {

		$options = array_merge(['once' => true], $options);

		if (is_array($url)) {
			$out = '';
			foreach ($url as $i) {
				$out .= "\n\t" . $this->script($i, $options);
			}

			return $out . "\n";

		}
		if ($options['once'] && isset($this->_includedScripts[$url])) {
			return null;
		}
		$this->_includedScripts[$url] = true;

		if (strpos($url, '//') === false) {
			$url = $this->generateAssetUrl($url, $options + array('pathPrefix' => 'js', 'ext' => '.js'));
			$options = array_diff_key($options, array('fullBase' => null, 'pathPrefix' => null));
		}
		$out = $this->formatTemplate('javascriptlink', [
			'url' => $url,
			'attrs' => $this->formatTemplateAttributes($options, ['once']),
		]);

		return $out;

	}

	/**
	 * Wrap $script in a script tag.
	 *
	 * ### Options
	 *
	 * - `safe` (boolean) Whether or not the $script should be wrapped in <![CDATA[ ]]>
	 * - `block` Set to true to append output to view block "script" or provide
	 *   custom block name.
	 *
	 * @param string $script The script to wrap
	 * @param array $options The options to use. Options not listed above will be
	 *    treated as HTML attributes.
	 * @return mixed string or null depending on the value of `$options['block']`
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::scriptBlock
	 */
	public function scriptBlock($script, $options = array()) {

		return $this->formatTemplate('javascriptblock', [
			'attrs' => $options,
			'content' => $script
		]);

	}

	/**
	 * Builds CSS style data from an array of CSS properties
	 *
	 * ### Usage:
	 *
	 * {{{
	 * echo $this->Html->style(array('margin' => '10px', 'padding' => '10px'), true);
	 *
	 * // creates
	 * 'margin:10px;padding:10px;'
	 * }}}
	 *
	 * @param array $data Style data array, keys will be used as property names, values as property values.
	 * @param boolean $oneline Whether or not the style block should be displayed on one line.
	 * @return string CSS styling data
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::style
	 */
	public function style($data, $oneline = true) {

		if (!is_array($data)) {
			return $data;
		}
		$out = array();
		foreach ($data as $key => $value) {
			$out[] = $key . ':' . $value . ';';
		}
		if ($oneline) {
			return implode(' ', $out);
		}

		return implode("\n", $out);

	}

	/**
	 * Creates a formatted IMG element.
	 *
	 * This method will set an empty alt attribute if one is not supplied.
	 *
	 * ### Usage:
	 *
	 * Create a regular image:
	 *
	 * `echo $this->Html->image('cake_icon.png', array('alt' => 'CakePHP'));`
	 *
	 * Create an image link:
	 *
	 * `echo $this->Html->image('cake_icon.png', array('alt' => 'CakePHP', 'url' => 'http://cakephp.org'));`
	 *
	 * ### Options:
	 *
	 * - `url` If provided an image link will be generated and the link will point at
	 *   `$options['url']`.
	 * - `fullBase` If true the src attribute will get a full address for the image file.
	 * - `plugin` False value will prevent parsing path as a plugin
	 *
	 * @param string $path Path to the image file, relative to the app/webroot/img/ directory.
	 * @param array $options Array of HTML attributes. See above for special options.
	 * @return string completed img tag
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::image
	 */
	public function image($path, $options = array()) {

		$path = $this->generateAssetUrl($path, $options + array('pathPrefix' => 'img'));
		$options = array_diff_key($options, array('fullBase' => null, 'pathPrefix' => null));

		if (!isset($options['alt'])) {
			$options['alt'] = '';
		}

		$url = false;
		if (!empty($options['url'])) {
			$url = $options['url'];
			unset($options['url']);
		}

		$image = $this->formatTemplate('image', [
			'url' => $path,
			'attrs' => $options
		]);

		if ($url) {
			return $this->formatTemplate('link', [
				'url' => $this->generateUrl($this->app, $url),
				'attrs' => null,
				'content' => $image
			]);
		}

		return $image;

	}

	/**
	 * Returns a row of formatted and named TABLE headers.
	 *
	 * @param array $names Array of tablenames. Each tablename also can be a key that points to an array with a set
	 *     of attributes to its specific tag
	 * @param array $trOptions HTML options for TR elements.
	 * @param array $thOptions HTML options for TH elements.
	 * @return string Completed table headers
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::tableHeaders
	 */
	public function tableHeaders($names, $trOptions = null, $thOptions = null) {

		$out = array();
		foreach ($names as $arg) {
			if (!is_array($arg)) {
				$out[] = $this->formatTemplate('tableheader', [
					'attrs' => $thOptions,
					'content' => $arg
				]);
			} else {
				$out[] = $this->formatTemplate('tableheader', [
					'attrs' => current($arg),
					'content' => key($arg)
				]);
			}
		}

		return $this->formatTemplate('tablerow', [
			'attrs' => $trOptions,
			'content' => implode(' ', $out)
		]);

	}

	/**
	 * Returns a formatted string of table rows (TR's with TD's in them).
	 *
	 * @param array $data Array of table data
	 * @param array $oddTrOptions HTML options for odd TR elements if true useCount is used
	 * @param array $evenTrOptions HTML options for even TR elements
	 * @param boolean $useCount adds class "column-$i"
	 * @param boolean $continueOddEven If false, will use a non-static $count variable,
	 *    so that the odd/even count is reset to zero just for that call.
	 * @return string Formatted HTML
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::tableCells
	 */
	public function tableCells($data, $oddTrOptions = null, $evenTrOptions = null, $useCount = false, $continueOddEven = true) {

		if (empty($data[0]) || !is_array($data[0])) {
			$data = array($data);
		}

		if ($oddTrOptions === true) {
			$useCount = true;
			$oddTrOptions = null;
		}

		if ($evenTrOptions === false) {
			$continueOddEven = false;
			$evenTrOptions = null;
		}

		if ($continueOddEven) {
			static $count = 0;
		} else {
			$count = 0;
		}

		foreach ($data as $line) {
			$count++;
			$cellsOut = array();
			$i = 0;
			foreach ($line as $cell) {
				$cellOptions = array();

				if (is_array($cell)) {
					$cellOptions = $cell[1];
					$cell = $cell[0];
				} elseif ($useCount) {
					$cellOptions['class'] = 'column-' . ++$i;
				}
				$cellsOut[] = $this->formatTemplate('tablecell', [
					'attrs' => $cellOptions,
					'content' => $cell
				]);
			}
			$opts = $count % 2 ? $oddTrOptions : $evenTrOptions;
			$out[] = $this->formatTemplate('tablerow', [
				'attrs' => $opts,
				'content' => implode(' ', $cellsOut),
			]);
		}

		return implode("\n", $out);

	}

	/**
	 * Returns a formatted block tag, i.e DIV, SPAN, P.
	 *
	 * ### Options
	 *
	 * - `escape` Whether or not the contents should be html_entity escaped.
	 *
	 * @param string $name Tag name.
	 * @param string $text String content that will appear inside the div element.
	 *   If null, only a start tag will be printed
	 * @param array $options Additional HTML attributes of the DIV tag, see above.
	 * @return string The formatted tag element
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::tag
	 */
	public function tag($name, $text = null, array $options = []) {

		if (empty($name)) {
			return $text;
		}
		if (isset($options['escape']) && $options['escape']) {
			$text = $this->app->escape($text);
			unset($options['escape']);
		}
		if ($text === null) {
			$tag = 'tagstart';
		} else {
			$tag = 'tag';
		}

		return $this->formatTemplate($tag, [
			'attrs' => $options,
			'tag' => $name,
			'content' => $text,
		]);

	}

	/**
	 * Returns a formatted DIV tag for HTML FORMs.
	 *
	 * ### Options
	 *
	 * - `escape` Whether or not the contents should be html_entity escaped.
	 *
	 * @param string $class CSS class name of the div element.
	 * @param string $text String content that will appear inside the div element.
	 *   If null, only a start tag will be printed
	 * @param array $options Additional HTML attributes of the DIV tag
	 * @return string The formatted DIV element
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::div
	 */
	public function div($class = null, $text = null, array $options = []) {

		if (!empty($class)) {
			$options['class'] = $class;
		}

		return $this->tag('div', $text, $options);

	}

	/**
	 * Returns a formatted P tag.
	 *
	 * ### Options
	 *
	 * - `escape` Whether or not the contents should be html_entity escaped.
	 *
	 * @param string $class CSS class name of the p element.
	 * @param string $text String content that will appear inside the p element.
	 * @param array $options Additional HTML attributes of the P tag
	 * @return string The formatted P element
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::para
	 */
	public function para($class, $text, array $options = []) {

		if (isset($options['escape'])) {
			$text = $this->app->escape($text);
		}
		if ($class && !empty($class)) {
			$options['class'] = $class;
		}
		$tag = 'para';
		if ($text === null) {
			$tag = 'parastart';
		}

		return $this->formatTemplate($tag, [
			'attrs' => $options,
			'content' => $text,
		]);

	}

	/**
	 * Build a nested list (UL/OL) out of an associative array.
	 *
	 * @param array $list Set of elements to list
	 * @param array $options Additional HTML attributes of the list (ol/ul) tag or if ul/ol use that as tag
	 * @param array $itemOptions Additional HTML attributes of the list item (LI) tag
	 * @param string $tag Type of list tag to use (ol/ul)
	 * @return string The nested list
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/html.html#HtmlHelper::nestedList
	 */
	public function nestedList($list, $options = array(), $itemOptions = array(), $tag = 'ul') {

		if (is_string($options)) {
			$tag = $options;
			$options = array();
		}
		$items = $this->_nestedListItem($list, $options, $itemOptions, $tag);

		return $this->formatTemplate($tag, [
			'attrs' => $options,
			'content' => $items
		]);

	}

	/**
	 * Internal function to build a nested list (UL/OL) out of an associative array.
	 *
	 * @param array $items Set of elements to list
	 * @param array $options Additional HTML attributes of the list (ol/ul) tag
	 * @param array $itemOptions Additional HTML attributes of the list item (LI) tag
	 * @param string $tag Type of list tag to use (ol/ul)
	 * @return string The nested list element
	 * @see HtmlHelper::nestedList()
	 */
	protected function _nestedListItem($items, $options, $itemOptions, $tag) {

		$out = '';

		$index = 1;
		foreach ($items as $key => $item) {
			if (is_array($item)) {
				$item = $key . $this->nestedList($item, $options, $itemOptions, $tag);
			}
			if (isset($itemOptions['even']) && $index % 2 === 0) {
				$itemOptions['class'] = $itemOptions['even'];
			} elseif (isset($itemOptions['odd']) && $index % 2 !== 0) {
				$itemOptions['class'] = $itemOptions['odd'];
			}
			$out .= $this->formatTemplate('li', [
				'attrs' => $this->formatTemplateAttributes($itemOptions, ['even', 'odd']),
				'content' => $item
			]);
			$index++;
		}

		return $out;

	}

}
