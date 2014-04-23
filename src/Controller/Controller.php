<?php

namespace Layer\Controller;

use Layer\Application;
use Layer\Controller\Action\ActionInterface;
use Layer\View\Twig\View;
use Layer\View\ViewInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class Controller
 *
 * @package Layer\Controller
 */
class Controller extends ControllerCollection implements ControllerInterface {

	/**
	 * @var \Layer\Application
	 */
	protected $app;

	/**
	 * @var string
	 */
	protected $plugin;

	/**
	 * @var string
	 */
	protected $_name;

	/**
	 * Request parameter to use for template
	 *
	 * @var string
	 */
	protected $_templateParam = 'action';

	/**
	 * @param Application $app
	 */
	public function __construct(Application $app) {

		$this->app = $app;
	}

	public function setName($name) {
		$this->_name = $name;
	}

	public function getName() {
		if ($this->_name === null) {
			$class = get_class($this);
			$pos = strrpos($class, '\\');
			if ($pos !== false) {
				$class = substr($class, $pos + 1);
			}
			$this->_name = $this->app['inflector']->underscore(preg_replace('/(Controller)$/', '', $class));
		}
		return $this->_name;
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function dispatch(Request $request) {

		$data = $this->invoke($request) ?: [];

		if($data instanceof Response) {
			return $data;
		}

		$view = $this->getView($request->get($this->_templateParam));

		return $this->_render($view, $data);
	}

	/**
	 * @param ViewInterface $view
	 * @param $data
	 * @return mixed
	 */
	protected function _render(ViewInterface $view, $data) {

		return $view->render($data);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function invoke(Request $request) {

		$callable = $this->getCallable($request);

		if (!is_callable($callable)) {
			throw new BadRequestHttpException;
		}

		return call_user_func_array($callable, [$this->app, $request]);
	}

	/**
	 * @param Request $request
	 * @return callable|false
	 */
	public function getCallable(Request $request) {

		if ($action = $request->get('action')) {

			$method = $this->app['inflector']->camelize($action) . 'Action';
			return [$this, $method];
		}

		return false;
	}

	/**
	 * @param Request $request
	 * @return View
	 */
	public function getView($name) {

		$template = $this->getTemplate($name);

		return new View($this->app, $template);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getTemplate($name) {

		$template = $this->name . '/' . $name;

		if ($this->plugin) {
			$template = '@' . $this->plugin . '/' . $template;
		}

		return $template;
	}

	/**
	 * @return mixed
	 */
	public function getPlugin() {
		return $this->plugin;
	}

}
