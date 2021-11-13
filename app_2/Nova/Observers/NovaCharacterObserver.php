<?php

namespace App\Nova\Observers;

use App\Models\Character;

class NovaCharacterObserver
{
    /**
     * Handle the Character "created" event.
     *
     * @param  \App\Models\Character  $character
     * @return void
     */
    public function created(Character $character)
    {
        $character->eventTemplate->characters_json = array_merge($character->eventTemplate->characters_json, [$character->id]);
        $character->eventTemplate->save();
    }

    /**
     * Handle the Character "updated" event.
     *
     * @param  \App\Models\Character  $character
     * @return void
     */
    public function updated(Character $character)
    {
        //
    }

    /**
     * Handle the Character "deleted" event.
     *
     * @param  \App\Models\Character  $character
     * @return void
     */
    public function deleted(Character $character)
    {
        $eventTemplate = $character->eventTemplate;
        $eventTemplate->characters_json = collect($eventTemplate->characters_json)->forget($character->id)->unique()->all();
        $eventTemplate->save();
    }

    /**
     * Handle the Character "restored" event.
     *
     * @param  \App\Models\Character  $character
     * @return void
     */
    public function restored(Character $character)
    {
        $eventTemplate = $character->eventTemplate;
        $eventTemplate->characters_json = collect($eventTemplate->characters_json)->push($character->id)->unique()->all();
        $eventTemplate->save();
    }

    /**
     * Handle the Character "force deleted" event.
     *
     * @param  \App\Models\Character  $character
     * @return void
     */
    public function forceDeleted(Character $character)
    {
        //
    }
}
