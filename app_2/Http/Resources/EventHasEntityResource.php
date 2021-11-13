<?php

namespace App\Http\Resources;

use App\Models\EventHasEntity;

/**
 * Class EventHasEntityResource.
 * @mixin EventHasEntity
 */
class EventHasEntityResource extends AbstractResource
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
            'entity_id' => $this->entity_id,
            'entity_type' => $this->entity_type,
        ];

        return $this->filtrateFields($this->filter($data));
    }
}
