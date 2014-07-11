define('cms-panel', ['jquery', 'cms-form'], function($, CmsForm) {

	'use strict';

	var CmsPanel = function(container) {

		this.container = container;
		this.container.data('cms-panel', this);
		this._init();

	};

	CmsPanel.prototype = {

		_init: function() {
			this.container.find('form').each(this._initForm.bind(this));
			this.container.trigger('cmsPanelInit');
		},

		_initForm: function(i, el) {
			return new CmsForm($(el));
		}

	};

	return CmsPanel;

});