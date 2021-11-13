<?php

namespace App\Http\Resources;

use App\Models\Character;

/**
 * Class CharacterResource.
 * @mixin Character
 */
class CharacterResource extends AbstractResource
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
