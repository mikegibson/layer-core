<?php

namespace Sentient\Action;

use Symfony\Component\HttpFoundation\Request;

class ReskinnedAction implements ActionInterface {

	/**
	 * @var ActionInterface
	 */
	private $baseAction;

	/**
	 * @var string
	 */
	private $template;

	/**
	 * @param ActionInterface $baseAction
	 * @param $template
	 */
	public function __construct(ActionInterface $baseAction, $template) {
		$this->baseAction = $baseAction;
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->getBaseAction()->getName();
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->getBaseAction()->getLabel();
	}

	/**
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @return bool
	 */
	public function isVisible() {
		return $this->getBaseAction()->isVisible();
	}

	/**
	 * @return bool
	 */
	public function isDirectlyAccessible() {
		return $this->getBaseAction()->isDirectlyAccessible();
	}

	/**
	 * @param Request $request
	 * @return array|null|\Symfony\Component\HttpFoundation\Response
	 */
	public function invoke(Request $request) {
		return $this->getBaseAction()->invoke($request);
	}

	/**
	 * @return ActionInterface
	 */
	protected function getBaseAction() {
		return $this->baseAction;
	}

}