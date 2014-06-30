<?php

namespace Sentient\Config\Driver;

interface ConfigDriver {

	function load($filename);

	function supports($filename);

}
