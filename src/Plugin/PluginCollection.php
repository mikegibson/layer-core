<?php

namespace Layer\Plugin;

use Layer\Application;
use Symfony\Component\Debug\Exception\FatalErrorException;

/**
 * Class PluginCollection
 *
 * @package Layer\Plugin
 */
class PluginCollection {

	/**
	 * @var \Silex\Application
	 */
	protected $app;

	/**
	 * @var array
	 */
	protected $_loaded = [];

	/**
	 * @var bool
	 */
	private $__booted = false;

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app) {

		$this->app = $app;
	}

	/**
	 * @param $plugin
	 * @return array|Plugin
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 */
	public function load($plugin) {

		$this->__checkNotBooted();

		if (is_array($plugin)) {
			return array_map([$this, 'load'], $plugin);
		}

		if (is_string($plugin)) {
			$name = $plugin;
			$pos = strrpos($plugin, '\\');
			if ($pos !== false) {
				$name = substr($name, $pos + 1);
			}
			$class = $plugin . '\\' . $name . 'Plugin';
			if (!class_exists($class)) {
				throw new \InvalidArgumentException(sprintf('Plugin class %s was not found!', $class));
			}
			$plugin = new $class($this->app);
		}

		if (!$plugin instanceof Plugin) {
			throw new \RuntimeException('Plugin classes must extend \\Layer\\Plugin!');
		}

		if ($this->loaded($plugin->name)) {
			throw new \InvalidArgumentException(sprintf('Plugin %s is already loaded!', $plugin->name));
		}

		$plugin->register();

		return $this->_loaded[$plugin->name] = $this->app['plugins.' . $plugin->name] = $plugin;
	}

	/**
	 * @param null $name
	 * @return array|bool
	 */
	public function loaded($name = null) {

		if ($name === null) {
			return array_keys($this->_loaded);
		}

		return isset($this->_loaded[$name]);
	}

	/**
	 * @param $name
	 * @return bool|Plugin
	 */
	public function get($name) {

		if (!$this->loaded($name)) {
			return false;
		}

		return $this->_loaded[$name];
	}

	/**
	 * Boot all loaded plugins
	 */
	public function boot() {

		$this->__checkNotBooted();
		$this->_checkDependencies();
		$this->__booted = false;
		foreach ($this->_loaded as $plugin) {
			$plugin->boot();
		}
	}

	/**
	 * Get dependencies of all loaded plugins
	 *
	 * @return array An associative array of dependencies and the plugins which depend on them
	 */
	public function getDependencies() {

		$dependencies = [];
		foreach ($this->loaded() as $name) {
			foreach ($this->get($name)->getDependencies() as $dep) {
				$dependencies[$dep][] = $name;
			}
		}

		return $dependencies;
	}

	protected function _checkDependencies() {

		if ($missing = array_diff_key($this->getDependencies(), $this->_loaded)) {
			foreach ($missing as $dep => $plugins) {
				$messages[] = sprintf('Plugin %s is required by plugin %s!', $dep, implode(', ', $plugins));
			}
			throw new FatalErrorException(implode('; ', $messages));
		}
	}

	private function __checkNotBooted() {

		if ($this->__booted) {
			throw new FatalErrorException('The app has already booted!');
		}
	}

}