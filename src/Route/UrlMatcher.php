<?php

namespace Sentient\Route;

use Silex\RedirectableUrlMatcher;
use Symfony\Component\Routing\Route as BaseRoute;
use Symfony\Component\Routing\RouteCollection;

class UrlMatcher extends RedirectableUrlMatcher {

	/**
	 * Tries to match a URL with a set of routes.
	 *
	 * @param string          $pathinfo The path info to be parsed
	 * @param RouteCollection $routes   The set of routes
	 *
	 * @return array An array of parameters
	 *
	 * @throws ResourceNotFoundException If the resource could not be found
	 * @throws MethodNotAllowedException If the resource was found but the request method is not allowed
	 */
	protected function matchCollection($pathinfo, RouteCollection $routes) {
		foreach ($routes as $name => $route) {
			$match = $this->matchRoute($pathinfo, $name, $route);
			if($match !== null) {
				return $match;
			}
		}
	}

	/**
	 * Tries to match a URL with an individual route.
	 *
	 * @param $pathinfo
	 * @param $name
	 * @param BaseRoute $route
	 * @return array|null
	 */
	protected function matchRoute($pathinfo, $name, BaseRoute $route) {
		$compiledRoute = $route->compile();

		// check the static prefix of the URL first. Only use the more expensive preg_match when it matches
		if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($pathinfo, $compiledRoute->getStaticPrefix())) {
			return null;
		}

		if (!preg_match($compiledRoute->getRegex(), $pathinfo, $matches)) {
			return null;
		}

		$hostMatches = array();
		if ($compiledRoute->getHostRegex() && !preg_match($compiledRoute->getHostRegex(), $this->context->getHost(), $hostMatches)) {
			return null;
		}

		// check HTTP method requirement
		if ($req = $route->getRequirement('_method')) {
			// HEAD and GET are equivalent as per RFC
			if ('HEAD' === $method = $this->context->getMethod()) {
				$method = 'GET';
			}

			if (!in_array($method, $req = explode('|', strtoupper($req)))) {
				$this->allow = array_merge($this->allow, $req);

				return null;
			}
		}

		$status = $this->handleRouteRequirements($pathinfo, $name, $route);

		if (self::ROUTE_MATCH === $status[0]) {
			return $status[1];
		}

		if (self::REQUIREMENT_MISMATCH === $status[0]) {
			return null;
		}

		$attrs = $this->getAttributes($route, $name, array_replace($matches, $hostMatches));

		if($route instanceof Route) {
			foreach($route->getMatchCallbacks() as $callback) {
				$ret = call_user_func($callback, $attrs);
				if($ret === false) {
					return null;
				}
				if(is_array($ret)) {
					$attrs = $ret;
				}
			}
		}

		return $attrs;

	}

}