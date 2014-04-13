<?php

namespace Layer\Route;

use Layer\Application;

/**
 * Class UrlGeneratorTrait
 *
 * @package Layer\Route
 */
trait UrlGeneratorTrait {

    /**
     * @param Application $app
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    public function generateUrl(Application $app, $name, array $parameters = []) {

        if (is_array($name)) {
            $parameters = $name;
            if (!isset($parameters['name'])) {
                trigger_error('No name was specified!');

                return false;
            }
            $name = $parameters['name'];
            unset($parameters['name']);
        }
        $args = array_merge([$name, $parameters], array_slice(func_get_args(), 3));

        return call_user_func_array([$app['url_generator'], 'generate'], $args);
    }

}