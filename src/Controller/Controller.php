<?php

namespace Layer\Controller;

use Layer\Application;
use Layer\Twig\TwigView;
use Layer\View\ViewInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
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
	protected $name;

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
		if (!$this->name) {
			$class = get_class($this);
			$pos = strrpos($class, '\\');
			if ($pos !== false) {
				$class = substr($class, $pos + 1);
			}
			$this->name = $app['inflector']->underscore(preg_replace('/(Controller)$/', '', $class));
		}
	}

	/**
	 * @param Request $request
	 * @return string
	 */
	public function dispatch(Request $request) {

		$data = $this->invoke($request);
		$view = $this->getView($request);

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

		return call_user_func($callable, $request);
	}

	/**
	 * @param Request $request
	 * @return callable|false
	 */
	public function getCallable(Request $request) {

		if ($action = $request->get('action')) {
			$method = $this->app['inflector']->camelize($action) . 'Action';

			if (!method_exists($this, $method)) {
				return false;
			}

			return [$this, $method];
		}

		return false;
	}

	/**
	 * @param Request $request
	 * @return TwigView
	 */
	public function getView(Request $request) {

		$template = $this->getTemplate($request);

		return new TwigView($this->app, $template);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getTemplate(Request $request) {

		$template = $this->name . '/' . $request->get($this->_templateParam);

		if ($this->plugin) {
			$template = '@' . $this->plugin . '/' . $template;
		}

		return $template;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getPlugin() {
		return $this->plugin;
	}

}
