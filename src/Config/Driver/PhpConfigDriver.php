<?php

namespace Layer\Config\Driver;

class PhpConfigDriver implements ConfigDriver {

	public function load($filename) {

		$config = require $filename;
		$config = (1 === $config) ? array() : $config;

		return $config ? : array();
	}

	public function supports($type) {
		return $type === 'php';
	}

}
