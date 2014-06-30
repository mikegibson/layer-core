<?php

namespace Sentient\View\Twig;

/**
 * Class TwigLoader
 *
 * @package Sentient\View\Twig
 */
class Loader extends \Twig_Loader_Filesystem {

	/**
	 * @var string
	 */
	protected $ext = 'twig';

	/**
	 * @param $ext
	 */
	public function setExt($ext) {
		$this->ext = $ext;
	}

	/**
	 * @return string
	 */
	public function getExt() {
		return $this->ext;
	}

	/**
	 * @param $name
	 * @return mixed|string
	 */
	protected function normalizeName($name) {
		$name = parent::normalizeName($name);
		if ($ext = $this->getExt()) {
			$suffix = '.' . $ext;
			if (substr($name, -strlen($suffix)) !== $suffix) {
				$name .= $suffix;
			}
		}
		return $name;
	}

}