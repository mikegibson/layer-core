require(['jquery', 'dropdown'], function($, Dropdown) {

	'use strict';

	var header;

	var CmsHeader = function() {
		this.el = $('#cms-header');
		this.navList = this.el.find('nav > ul');
		this.dropdown = this.navList.length ? new Dropdown(this.navList) : null;
	};

	header = new CmsHeader();

});