<?php

namespace App\Http\Resources;

use App\Models\EventTemplate;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class EventTemplateResource.
 * @mixin EventTemplate
 */
class EventTemplateResource extends JsonResource
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
            'duration' => $this->duration_in_hour,
            'color' => $this->color,
            'item_order' => $this->item_order,
            'event_type' => $this->eventType->title,
            'player_groups' => $this->playerGroups ? new PlayerGroupCollection($this->playerGroups) : [],
            'characters' => $this->characters ? new CharacterCollection($this->characters) : [],
        ];
    }
}
