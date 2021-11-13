<?php

namespace App\Http;

class RouteItem
{
    /**
     * @var string GET, POST, DELETE, PUT, PATCH
     */
    private $method;

    private $exceptMiddleware;

    /**
     * @var string test/index for e.g
     */
    private $uri;

    /**
     * @var string TestController@index for e.g
     */
    private $controllerAction;

    /** @var string test.index for e.g */
    private $name;

    /**
     * RouteItem constructor.
     * @param $method
     * @param $uri
     * @param $controllerAction
     * @param $name
     * @param mixed $exceptMiddleware
     */
    public function __construct($method, $uri, $controllerAction, $name, $exceptMiddleware = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->controllerAction = $controllerAction;
        $this->name = $name;
        $this->exceptMiddleware = $exceptMiddleware;
    }

    /**
     * @return mixed
     */
    public function getExceptMiddleware()
    {
        return $this->exceptMiddleware;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
