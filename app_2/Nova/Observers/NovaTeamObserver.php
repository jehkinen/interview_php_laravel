<?php

namespace App\Nova\Observers;

use App\Models\Team;

class NovaTeamObserver
{
    public function saving(Team $team)
    {
        if ($team->players_json) {
            $team->players_json = array_map('intval', json_decode($team->players_json));
        }
    }

    /**
     * Handle the Team "created" event.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function created(Team $team)
    {
        //
    }

    /**
     * Handle the Team "updated" event.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function updated(Team $team)
    {
        $team->teamPlayers()->delete();

        if ($team->players_json) {
            foreach ($team->players_json as $i => $playerId) {
                $team->teamPlayers()->create([
                    'player_id' => $playerId,
                    'started_at' => now(),
                ]);
            }
        }
    }

    /**
     * Handle the Team "deleted" event.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function deleted(Team $team)
    {
        //
    }

    /**
     * Handle the Team "restored" event.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function restored(Team $team)
    {
        //
    }

    /**
     * Handle the Team "force deleted" event.
     *
     * @param  \App\Models\Team  $team
     * @return void
     */
    public function forceDeleted(Team $team)
    {
        //
    }
}
