<?php

namespace App\Http\Resources;

use App\Models\Event;

/**
 * Class EventResource.
 * @mixin Event
 */
class EventResource extends AbstractResource
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
            'title' => $this->title,
            'id' => $this->id,
            'studio_id' => $this->studio_id,
            'event_template_id' => $this->event_template_id,
            'specific_data' => $this->specific_data,
            'duration' => $this->duration->format('H:i'),
            'color' => $this->color,
            'event_id' => $this->studio_id,
            'start_at' => $this->start_at->timestamp,
            'players' => new PlayerCollection($this->players),
            'player_groups' => new PlayerGroupCollection($this->playerGroups),

            $this->merge(new EventNoteResource($this->note)),
            'private_note_players_access' => new PlayerCollection($this->privateNotePlayers),
            'specific_person' => new EventTypeSpecificPersonResource($this->specificPerson),
        ];

        foreach ($this->hasCharacters as $hasCharacter) {
            $data['characters'][] = (new EventHasCharacterResource($hasCharacter));
        }

        return $this->filtrateFields($this->filter($data));
    }
}
