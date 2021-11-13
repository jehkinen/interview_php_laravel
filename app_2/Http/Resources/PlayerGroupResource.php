<?php

namespace App\Http\Resources;

use App\Models\PlayerGroup;

/**
 * Class UserGroupResource.
 * @mixin PlayerGroup
 */
class PlayerGroupResource extends AbstractResource
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
            'players' => $this->whenLoaded('players', new PlayerCollection($this->players))
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
