<?php

namespace App\Http\Resources;

use App\Models\Studio;

/**
 * Class StudioResource.
 * @mixin Studio
 */
class StudioResource extends AbstractResource
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
            'capacity' => $this->capacity,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
