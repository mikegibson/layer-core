require(['jquery', 'cms-panel'], function($, CmsPanel) {

	'use strict';

	function Cms() {
		this.container = $('#container');
		this.container.find('.panel.cms').each(function() {
			return new CmsPanel($(this));
		});
	}

	window.cms = new Cms();

});