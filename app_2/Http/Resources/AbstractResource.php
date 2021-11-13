<?php

namespace App\Http\Resources;

use App\Traits\FilterableResourceTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractResource extends JsonResource
{
    use FilterableResourceTrait;

    public function __construct($resource)
    {
        ResourceCollection::withoutWrapping();
        parent::__construct($resource);
    }
}
