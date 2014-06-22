<?php

namespace Layer\Action;

use Layer\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ActionDispatcher
 * @package Layer\Action
 */
class ActionDispatcher {

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	protected $eventDispatcher;

	/**
	 * @var \Layer\View\ViewInterface
	 */
	protected $view;

	/**
	 * @param EventDispatcherInterface $eventDispatcher
	 * @param ViewInterface $view
	 */
	public function __construct(EventDispatcherInterface $eventDispatcher, ViewInterface $view) {
		$this->eventDispatcher = $eventDispatcher;
		$this->view = $view;
	}

	/**
	 * @param ActionInterface $action
	 * @param Request $request
	 * @return array
	 */
	public function dispatch(ActionInterface $action, Request $request) {
		$event = new ActionEvent($action, $request, $this->view);
		$this->eventDispatcher->dispatch(ActionEvent::BEFORE_DISPATCH, $event);
		$result = $event->getResult();
		if($result === null) {
			$result = $event->getAction()->invoke($request) ?: [];
		}
		$event->setResult($result);
		if($result instanceof Response) {
			$event->setResponse($result);
		} else {
			$this->eventDispatcher->dispatch(ActionEvent::BEFORE_RENDER, $event);
			$result = $event->getResult();
			if($result instanceof Response) {
				$event->setResponse($result);
			} else {
				if(!is_array($result)) {
					$result = [];
				}
				$result['action'] = $action;
				$event->setResponse($event->getView()->render($event->getTemplate(), $result));
			}
		}
		$this->eventDispatcher->dispatch(ActionEvent::AFTER_DISPATCH, $event);
		return $event->getResponse();
	}

}