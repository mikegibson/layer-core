<?php

namespace Sentient\Data;

use Doctrine\Common\Persistence\AbstractManagerRegistry;
use Silex\Application;

/**
 * References Doctrine connections and entity/document managers.
 */
class ManagerRegistry extends AbstractManagerRegistry {

	/**
	 * @var Application
	 */
	private $app;

	public function __construct(
		Application $app,
		$name,
		array $connections,
		array $managers,
		$defaultConnection,
		$defaultManager,
		$proxyInterfaceName
	) {
		$this->app = $app;
		parent::__construct($name, $connections, $managers, $defaultConnection, $defaultManager, $proxyInterfaceName);
	}

	protected function getService($name) {
		return $this->app[$name];
	}

	protected function resetService($name) {
		unset($this->app[$name]);
	}

	public function getAliasNamespace($alias) {
		throw new \BadMethodCallException('Namespace aliases not supported.');
	}

}