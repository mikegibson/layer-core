<?php

namespace Layer\Admin\View;

use Layer\Data\DataType;
use Silex\Application;

class AdminHelper {

	protected $app;

	public function __construct(Application $app) {

		$this->app = $app;

	}

	public function link($dataType, $action = 'index', $label = 'List', array $urlParams = [], array $linkOptions = []) {

		if (!$dataType = $this->_getDataType($dataType)) {
			trigger_error('You must pass a data type!');

			return false;
		}

		$url = $this->url($dataType, $action, $urlParams);

		return $this->app['html_helper']->link($label, $url, $linkOptions);

	}

	public function url($dataType, $action = 'index', array $params = [], $generate = false) {

		if (!$dataType = $this->_getDataType($dataType)) {
			trigger_error('You must pass a data type!');

			return false;
		}

		if (is_array($action)) {
			$params = $action;
		} else {
			$params['action'] = $action;
		}

		$params['namespace'] = $dataType->namespace;
		$params['type'] = $dataType->slug;

		$params['name'] = 'admin_scaffold';

		if (!$generate) {
			return $params;
		}

		return $this->app['url_generator']->generateUrl('admin_scaffold', $params);

	}

	protected function _getDataType($dataType) {

		if (is_string($dataType)) {
			$dataType = $this->app['data']->get($dataType);
		}

		if (!$dataType instanceof DataType) {
			return false;
		}

		return $dataType;

	}

}