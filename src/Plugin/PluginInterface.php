<?php

namespace Sentient\Plugin;

use Silex\ServiceProviderInterface;

interface PluginInterface extends ServiceProviderInterface {

	public function getName();

	public function getPath();

}