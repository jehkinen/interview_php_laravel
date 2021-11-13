<?php

namespace App\Http\Resources;

class EventCollection extends AbstractResourceCollection
{
    public $collects = EventResource::class;

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
