<?php

namespace Layer\Admin\View;

use Layer\Data\ManagedRepositoryInterface;
use Silex\Application;

class AdminHelper {

	protected $app;

	public function __construct(Application $app) {

		$this->app = $app;

	}

	public function url(ManagedRepositoryInterface $repository, $action = 'index', array $params = []) {

		if (is_array($action)) {
			$params = $action;
		} else {
			$params['action'] = $action;
		}

		$params['namespace'] = $repository->getNamespace();
		$params['type'] = $repository->getBasename();

		return $this->app['url_generator']->generate('admin_scaffold', $params);

	}

}