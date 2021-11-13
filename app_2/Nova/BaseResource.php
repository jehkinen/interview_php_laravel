<?php

namespace App\Nova;

abstract class BaseResource extends Resource
{
    protected $queryParams;

    public function __construct($resource)
    {
        parent::__construct($resource);
        parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $queries);
        $this->queryParams = collect($queries);
    }
}
