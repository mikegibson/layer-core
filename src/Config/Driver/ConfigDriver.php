<?php

namespace Layer\Config\Driver;

interface ConfigDriver {

	function load($filename);

	function supports($filename);

}
