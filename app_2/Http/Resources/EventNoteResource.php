<?php

namespace App\Http\Resources;

use App\Models\EventNote;

/**
 * Class EventNoteResource.
 * @mixin EventNote
 */
class EventNoteResource extends AbstractResource
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
            'public_note' => $this->public_note,
            'private_note' => $this->private_note,
        ];
    }
}
