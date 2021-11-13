<?php

namespace App\Http\Resources;

use App\Models\SportPosition;

/**
 * Class SportPositionResource.
 * @mixin SportPosition
 */
class SportPositionResource extends AbstractResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'sport' => $this->sport->title,
        ];
    }
}
