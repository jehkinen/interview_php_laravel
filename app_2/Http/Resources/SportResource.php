<?php

namespace App\Http\Resources;

use App\Models\Sport;

/**
 * Class SportResource.
 * @mixin Sport
 */
class SportResource extends AbstractResource
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
            'title' => $this->title,
        ];
    }
}
