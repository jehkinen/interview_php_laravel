<?php

namespace App\Models;

/**
 * App\Models\TeamPlayer.
 *
 * @property int $id
 * @property int $team_id
 * @property int $player_id
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamPlayer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TeamPlayer extends BaseModel
{
    protected $fillable = [
        'team_id',
        'player_id',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
