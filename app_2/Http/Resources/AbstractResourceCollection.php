<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractResourceCollection extends ResourceCollection
{
    public function __construct($resource)
    {
        ResourceCollection::withoutWrapping();
        parent::__construct($resource);
    }
}
