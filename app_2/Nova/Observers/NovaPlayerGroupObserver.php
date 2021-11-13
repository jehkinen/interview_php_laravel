<?php

namespace App\Nova\Observers;

use App\Models\PlayerGroup;

class NovaPlayerGroupObserver
{
    /**
     * Handle the PlayerGroup "created" event.
     *
     * @param  \App\Models\PlayerGroup  $playerGroup
     * @return void
     */
    public function created(PlayerGroup $playerGroup)
    {
        $this->savePlayerGroupAssn($playerGroup);
    }

    private function savePlayerGroupAssn(PlayerGroup $playerGroup)
    {
        if ($playerGroup->players_json) {
            if (is_string($playerGroup->players_json)) {
                $groupsIds = json_decode($playerGroup->players_json, true);
            } else {
                $groupsIds = $playerGroup->players_json;
            }

            foreach ($groupsIds as $i => $playerId) {
                $playerGroup->playerGroupHasPlayers()->create([
                    'player_id' => $playerId,
                ]);
            }
        }
    }

    /**
     * Handle the PlayerGroup "updated" event.
     *
     * @param  \App\Models\PlayerGroup  $playerGroup
     * @return void
     */
    public function updated(PlayerGroup $playerGroup)
    {
        $playerGroup->playerGroupHasPlayers()->delete();
        $this->savePlayerGroupAssn($playerGroup);
    }

    /**
     * Handle the PlayerGroup "deleted" event.
     *
     * @param  \App\Models\PlayerGroup  $playerGroup
     * @return void
     */
    public function deleted(PlayerGroup $playerGroup)
    {
        //
    }

    /**
     * Handle the PlayerGroup "restored" event.
     *
     * @param  \App\Models\PlayerGroup  $playerGroup
     * @return void
     */
    public function restored(PlayerGroup $playerGroup)
    {
        //
    }

    /**
     * Handle the PlayerGroup "force deleted" event.
     *
     * @param  \App\Models\PlayerGroup  $playerGroup
     * @return void
     */
    public function forceDeleted(PlayerGroup $playerGroup)
    {
        //
    }
}
