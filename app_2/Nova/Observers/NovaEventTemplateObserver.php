<?php

namespace App\Nova\Observers;

use App\Models\EventTemplate;

class NovaEventTemplateObserver
{
    public function saving(EventTemplate $eventTemplate)
    {
        if ($eventTemplate->isDirty('item_order')) {
            return;
        }
        if (is_array($eventTemplate->player_groups_json)) {
            $eventTemplate->player_groups_json = array_map('intval', json_decode($eventTemplate->player_groups_json));
        }
    }

    /**
     * Handle the EventTemplate "updated" event.
     *
     * @param \App\Models\EventTemplate $eventTemplate
     * @return void
     */
    public function updated(EventTemplate $eventTemplate)
    {
        if ($eventTemplate->isDirty('item_order')) {
            return;
        }
        //@todo need to fix this bug
        $eventTemplate->hasPlayerGroups()->delete();

        if (is_array($eventTemplate->player_groups_json)) {
            foreach ($eventTemplate->player_groups_json as $key => $playerGroupId) {
                $eventTemplate->hasPlayerGroups()->create([
                    'player_group_id' => $playerGroupId,
                ]);
            }
        }
    }

    /**
     * Handle the EventTemplate "deleted" event.
     *
     * @param \App\Models\EventTemplate $eventTemplate
     * @return void
     */
    public function deleted(EventTemplate $eventTemplate)
    {
        //
    }

    /**
     * Handle the EventTemplate "restored" event.
     *
     * @param \App\Models\EventTemplate $eventTemplate
     * @return void
     */
    public function restored(EventTemplate $eventTemplate)
    {
        //
    }

    /**
     * Handle the EventTemplate "force deleted" event.
     *
     * @param \App\Models\EventTemplate $eventTemplate
     * @return void
     */
    public function forceDeleted(EventTemplate $eventTemplate)
    {
        //
    }
}
