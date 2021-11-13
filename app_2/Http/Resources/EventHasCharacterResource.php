<?php

namespace App\Http\Resources;

use App\Models\EventHasCharacter;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EventHasCharacterResource.
 * @mixin EventHasCharacter
 */
class EventHasCharacterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'player' => new PlayerResource($this->player),
            'character' => new CharacterResource($this->character),
        ];
    }
}
