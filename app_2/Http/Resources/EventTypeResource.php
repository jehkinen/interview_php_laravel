<?php

namespace App\Http\Resources;

use App\Models\EventType;

/**
 * Class EventTypeResource.
 * @mixin EventType
 */
class EventTypeResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'specific_labels' => $this->specific_labels,
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
