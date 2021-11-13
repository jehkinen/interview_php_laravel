<?php

namespace App\Http\Resources;

use App\Models\Event;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EventHasEntitiesCollection extends ResourceCollection
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
            foreach ($event->hasEntities as $entity) {
                $data[$event->id][] = (new EventHasEntityResource($entity));
            }
        });

        return $this->filter($data);
    }
}
