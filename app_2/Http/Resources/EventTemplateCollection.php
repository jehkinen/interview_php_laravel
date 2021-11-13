<?php

namespace App\Http\Resources;

class EventTemplateCollection extends AbstractResourceCollection
{
    public $collects = EventTemplateResource::class;

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
