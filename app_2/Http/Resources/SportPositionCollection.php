<?php

namespace App\Http\Resources;

class SportPositionCollection extends AbstractResourceCollection
{
    public $collects = SportPositionResource::class;

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
