define('dropdown', ['jquery'], function($) {

	'use strict';

	var Dropdown, DropdownItem,
		defaults = {
			duration: 100,
			openDelay: 20,
			closeDelay: 150,
			hoverEvent: 'mouseenter',
			unhoverEvent: 'mouseleave'
		};

	Dropdown = function(el, config) {
		this.config = $.extend({}, defaults, config);
		this.el = el;
		this.items = [];
		el.children('li').each(this._prepareChild.bind(this));
	};

	Dropdown.prototype = {

		_prepareChild: function(i, el) {
			this.items.push(new DropdownItem($(el), this.config));
		}

	};

	DropdownItem = function(el, config) {
		this.config = config;
		this.el = el;
		this.childList = this.el.children('ul');
		if(this.childList.length) {
			this._initChild();
		}
	};

	DropdownItem.prototype = {

		_initChild: function() {
			this.childList.css('display', 'block').hide();
			this.submenu = new Dropdown(this.childList, this.config);
			this.el
				.on(this.config.hoverEvent, this._handleHover.bind(this))
				.on(this.config.unhoverEvent, this._handleUnhover.bind(this));
		},

		_handleHover: function() {
			clearTimeout(this.timeout);
			this.timeout = setTimeout(this.open.bind(this), this.config.openDelay);
		},

		_handleUnhover: function() {
			clearTimeout(this.timeout);
			this.timeout = setTimeout(this.close.bind(this), this.config.closeDelay);
		},

		open: function() {
			this.childList.slideDown(this.config.duration);
		},

		close: function() {
			this.childList.slideUp(this.config.duration);
		}

	};

	return Dropdown;

});