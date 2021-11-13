<?php

namespace App\Services;

use App\Models\EventTemplate;

class EventTemplateService
{
    /**
     * @return EventTemplate|\App\Queries\EventTypeQueryBuilder
     */
    public function list()
    {
        return EventTemplate::fetchAll();
    }
}
