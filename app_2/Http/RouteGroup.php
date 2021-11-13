<?php

namespace App\Http;

class RouteGroup
{
    private $attributes;
    private $routesCollection;

    public function __construct($attributes, $routesCollection)
    {
        $this->attributes = $attributes;
        $this->routesCollection = $routesCollection;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return RouteItem[]
     */
    public function getRoutesCollection()
    {
        return $this->routesCollection;
    }
}
