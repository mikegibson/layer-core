<?php

namespace Layer\Config;

use Layer\Application;
use Layer\Config\Driver\ConfigDriver;
use Symfony\Component\Config\FileLocatorInterface;

class Configuration {

	protected $app;

	protected $_config = [];

	protected $_defaults = [];

	protected $_locators = [];

	protected $_drivers = [];

	public function __construct(Application $app) {

		$this->app = $app;

	}

	public function loadLocator(FileLocatorInterface $locator) {
		$this->_locators[] = $locator;
	}

	public function loadDriver(ConfigDriver $driver) {
		$this->_drivers[] = $driver;
	}

	public function load($name, array $options = []) {

		if (is_array($name)) {
			$r = true;
			foreach ($name as $_name => $_options) {
				if (!is_array($_options)) {
					$_name = $_options;
					$_options = [];
				}
				$_options = array_merge($options, $_options);
				$r = $this->load($_name, $_options) && $r;
			}
			return $r;
		}

		$options = array_merge([
			'type' => 'yml',
			'nest' => true,
			'replacements' => [],
			'ignoreMissing' => false
		], $options);

		if ($options['nest'] && !is_string($options['nest'])) {
			$options['nest'] = $name;
		}

		if (!isset($options['ext'])) {
			$options['ext'] = $options['type'];
		}

		$filename = $name . ($options['ext'] ? '.' . $options['ext'] : '');

		$found = false;
		foreach ($this->_locators as $locator) {
			try {
				$path = $locator->locate($filename);
			} catch (\InvalidArgumentException $e) {
				continue;
			}
			if ($path) {
				$found = true;
				break;
			}
		}

		if (!$found) {
			if ($options['ignoreMissing']) {
				return false;
			}
			throw new \InvalidArgumentException(sprintf("Config file '%s' was not found.", $filename));
		}

		$tokens = [];
		foreach ($options['replacements'] as $key => $value) {
			$tokens['%' . $key . '%'] = $value;
		}

		$supported = false;
		foreach ($this->_drivers as $driver) {
			if ($driver->supports($options['type'])) {
				$supported = true;
				break;
			}
		}

		if (!$supported) {
			throw new \InvalidArgumentException(
				sprintf("Config type '%s' is not supported!", $options['type']));
		}

		$config = $driver->load($path);

		$config = $this->_replace($config, $tokens);

		if ($options['nest']) {
			foreach (explode('.', $options['nest']) as $node) {
				$config = [$node => $config];
			}
		}

		$this->write($config);

		return true;

	}

	protected function _replace($node, array $tokens) {

		if (is_array($node)) {
			foreach ($node as $k => $v) {
				$node[$k] = $this->_replace($v, $tokens);
			}
		} elseif (is_string($node)) {
			$node = strtr($node, $tokens);
		}

		return $node;
	}

	public function read($key, $returnDefault = true) {
		$r = $this->app['array_helper']->get($this->_config, $key);
		if ($r === null && $returnDefault) {
			return $this->readDefault($key);
		}
		return $r;
	}

	public function readDefault($key) {
		return $this->app['array_helper']->get($this->_defaults, $key);
	}

	public function write($key, $value = null, $default = false) {

		if (is_array($key)) {
			foreach ($key as $_key => $_value) {
				$this->write($_key, $_value, $default);
			}
			return;
		}

		if ($default) {
			$config = & $this->_defaults;
		} else {
			$config = & $this->_config;
		}

		$node = $this->app['array_helper']->get($config, $key);

		if (is_array($node) && is_array($value)) {
			$node = $this->app['array_helper']->merge($node, $value);
		} else {
			$node = $value;
		}

		$config = $this->app['array_helper']->insert($config, $key, $node);

	}

	public function writeDefault($key, $value = null) {
		$this->write($key, $value, true);
	}

}