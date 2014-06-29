define(['jquery'], function($) {

	'use strict';

	var Form, FormRow, HtmlWidget;

	Form = function(form) {
		this.form = form;
		this.rows = {};
		this.form.find('.form_row').each(function(i, el) {
			this._initRow($(el));
		}.bind(this));
	};

	Form.prototype = {

		_initRow: function(row) {
			var formRow = new FormRow(row);
			this.rows[formRow.name] = formRow;
		}

	};

	FormRow = function(row) {

		this.row = row;
		this.type = row.data('type');
		this.name = row.data('name');
		this.widget = row.find('div.widget');

		switch(this.type) {
			case 'html':
				this.htmlWidget = new HtmlWidget(this.widget);
				break;
		}

	};

	HtmlWidget = function(widget) {
		this.widget = widget;
		this.textarea = widget.find('textarea');
	};

	return Form;

});