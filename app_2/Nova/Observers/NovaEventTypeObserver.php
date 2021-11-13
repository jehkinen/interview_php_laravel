<?php

namespace App\Nova\Observers;

use App\Models\EventType;
use App\Models\EventTemplate;

class NovaEventTypeObserver
{
    /**
     * Handle the EventType "created" event.
     *
     * @param  \App\Models\EventType  $eventType
     * @return void
     */
    public function created(EventType $eventType)
    {
        //
    }

    /**
     * Handle the EventType "updated" event.
     *
     * @param  \App\Models\EventType  $eventType
     * @return void
     */
    public function updated(EventType $eventType)
    {
        //
    }

    /**
     * Handle the EventType "deleted" event.
     *
     * @param  \App\Models\EventType  $eventType
     * @return void
     */
    public function deleted(EventType $eventType)
    {
        EventTemplate::query()
            ->where('event_type_id', $eventType->id)
            ->delete();
    }

    /**
     * Handle the EventType "restored" event.
     *
     * @param  \App\Models\EventType  $eventType
     * @return void
     */
    public function restored(EventType $eventType)
    {
        //
    }

    /**
     * Handle the EventType "force deleted" event.
     *
     * @param  \App\Models\EventType  $eventType
     * @return void
     */
    public function forceDeleted(EventType $eventType)
    {
        //
    }
}
