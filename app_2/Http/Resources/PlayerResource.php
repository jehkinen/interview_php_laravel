<?php

namespace App\Http\Resources;

use App\Models\Player;

/**
 * Class PlayerResource.
 * @mixin Player
 */
class PlayerResource extends AbstractResource
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
            'title' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'position_id' => $this->sport_position_id,
            'position' => new SportPositionResource($this->sportPosition),
        ];
    }
}
