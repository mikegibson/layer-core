<?php

namespace Sentient\Config\Driver;

use Symfony\Component\Yaml\Yaml;

class YamlConfigDriver implements ConfigDriver {

	public function load($filename) {

		$config = Yaml::parse($filename);

		return $config ? : array();
	}

	public function supports($type) {
		return $type === 'yml';
	}
}
