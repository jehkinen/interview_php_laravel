<?php

namespace App\Http\Resources;

use App\Traits\FilterableResourceTrait;

class PlayerCollection extends AbstractResourceCollection
{
    public $collects = PlayerResource::class;

    use FilterableResourceTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
