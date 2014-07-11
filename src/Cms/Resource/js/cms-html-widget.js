define('cms-html-widget', [
	'jquery',
	'scribe',
	'scribe-plugin-blockquote-command',
	'scribe-plugin-curly-quotes',
	'scribe-plugin-formatter-plain-text-convert-new-lines-to-html',
	'scribe-plugin-heading-command',
	'scribe-plugin-intelligent-unlink-command',
	'scribe-plugin-keyboard-shortcuts',
	'scribe-plugin-link-prompt-command',
	'scribe-plugin-sanitizer',
	'scribe-plugin-smart-lists',
	'scribe-plugin-toolbar'
], function (
	$,
	Scribe,
	scribePluginBlockquoteCommand,
	scribePluginCurlyQuotes,
	scribePluginFormatterPlainTextConvertNewLinesToHtml,
	scribePluginHeadingCommand,
	scribePluginIntelligentUnlinkCommand,
	scribePluginKeyboardShortcuts,
	scribePluginLinkPromptCommand,
	scribePluginSanitizer,
	scribePluginSmartLists,
	scribePluginToolbar
) {

	'use strict';

	var HtmlWidget = function(widget) {
		this.widget = widget;
		this.textarea = widget.find('textarea');
		this.element = widget.find('.scribe-content');
		this.toolbar = widget.find('.scribe-toolbar');
		this.scribe = new Scribe(this.element[0], { allowBlockElements: true });
		//this.scribe.use(scribePluginToolbar(this.toolbar[0]));

		/**
		 * Keyboard shortcuts
		 */

		var ctrlKey = function (event) { return event.metaKey || event.ctrlKey; };

		var commandsToKeyboardShortcutsMap = Object.freeze({
			bold: function (event) { return event.metaKey && event.keyCode === 66; }, // b
			italic: function (event) { return event.metaKey && event.keyCode === 73; }, // i
			strikeThrough: function (event) { return event.altKey && event.shiftKey && event.keyCode === 83; }, // s
			removeFormat: function (event) { return event.altKey && event.shiftKey && event.keyCode === 65; }, // a
			linkPrompt: function (event) { return event.metaKey && ! event.shiftKey && event.keyCode === 75; }, // k
			unlink: function (event) { return event.metaKey && event.shiftKey && event.keyCode === 75; }, // k,
			insertUnorderedList: function (event) { return event.altKey && event.shiftKey && event.keyCode === 66; }, // b
			insertOrderedList: function (event) { return event.altKey && event.shiftKey && event.keyCode === 78; }, // n
			blockquote: function (event) { return event.altKey && event.shiftKey && event.keyCode === 87; }, // w
			h1: function (event) { return ctrlKey(event) && event.keyCode === 49; }, // 1
			h2: function (event) { return ctrlKey(event) && event.keyCode === 50; }, // 2
			h3: function (event) { return ctrlKey(event) && event.keyCode === 51; }, // 3
			h4: function (event) { return ctrlKey(event) && event.keyCode === 52; }, // 4
		});

		/**
		 * Plugins
		 */

		this.scribe.use(scribePluginBlockquoteCommand());
		this.scribe.use(scribePluginHeadingCommand(1));
		this.scribe.use(scribePluginHeadingCommand(2));
		this.scribe.use(scribePluginHeadingCommand(3));
		this.scribe.use(scribePluginHeadingCommand(4));
		this.scribe.use(scribePluginIntelligentUnlinkCommand());
		this.scribe.use(scribePluginLinkPromptCommand());
		this.scribe.use(scribePluginToolbar(this.toolbar[0]));
		this.scribe.use(scribePluginSmartLists());
		this.scribe.use(scribePluginCurlyQuotes());
		this.scribe.use(scribePluginKeyboardShortcuts(commandsToKeyboardShortcutsMap));

		// Formatters
		this.scribe.use(scribePluginSanitizer({
			tags: {
				p: {},
				br: {},
				b: {},
				strong: {},
				i: {},
				strike: {},
				blockquote: {},
				ol: {},
				ul: {},
				li: {},
				a: { href: true },
				h1: {},
				h2: {},
				h3: {},
				h4: {}
			}
		}));
		this.scribe.use(scribePluginFormatterPlainTextConvertNewLinesToHtml());

		this.scribe.setContent(this.textarea.val());
		this.scribe.on('content-changed', this._updateTextarea.bind(this));

	};
//alert('hello');
	HtmlWidget.prototype = {

		_updateTextarea: function() {
			this.textarea.val(this.scribe.getHTML());
		}

	};

	return HtmlWidget;

});