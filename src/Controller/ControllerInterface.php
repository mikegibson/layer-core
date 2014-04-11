<?php

namespace Layer\Controller;

use Layer\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ControllerInterface
 *
 * @package Layer\Controller
 */
interface ControllerInterface {

    /**
     * @param Application $app
     */
    public function __construct(Application $app);

    /**
     * @param Request $request
     * @return string
     */
    public function dispatch(Request $request);

}