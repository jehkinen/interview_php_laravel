<?php

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EventHasCharacterCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [];
        $this->collection->each(function (Event $event) use ($data) {
            foreach ($event->hasCharacters as $hasCharacter) {
                $data[$event->id][] = (new EventHasCharacterResource($hasCharacter));
            }
        });

        return $this->filter($data);
    }
}
