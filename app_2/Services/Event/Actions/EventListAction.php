<?php

namespace App\Services\Event\Actions;

use App\Models\Event;
use App\Http\Resources\EventCollection;

class EventListAction
{
    public function run()
    {
        $events = Event::fetchAll();

        return new EventCollection($events);
    }
}
