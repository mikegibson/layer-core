<?php

namespace Sentient\Action;

use Symfony\Component\HttpFoundation\Request;

class SimpleAction implements ActionInterface {

	private $name;

	private $label;

	private $template;

	private $callable;

	private $isVisible;

	private $isDirectlyAccessible;

	public function __construct(
		$name,
		$label,
		$template = null,
		$callable = null,
		$isVisible = true,
		$isDirectlyAccessible = true
	) {
		if($callable !== null && !is_callable($callable)) {
			throw new \InvalidArgumentException('The argument was not a callable.');
		}
		$this->name = $name;
		$this->label = $label;
		$this->template = $template;
		$this->callable = $callable;
		$this->isVisible = $isVisible;
		$this->isDirectlyAccessible = $isDirectlyAccessible;
	}

	public function getName() {
		return $this->name;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function invoke(Request $request) {
		if($this->callable !== null) {
			return call_user_func($this->callable, $request);
		}
	}

	public function isVisible() {
		return $this->isVisible;
	}

	public function isDirectlyAccessible() {
		return $this->isDirectlyAccessible;
	}

}