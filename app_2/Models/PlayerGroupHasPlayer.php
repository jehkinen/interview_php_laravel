<?php

namespace App\Models;

/**
 * App\Models\PlayerGroupHasPlayer.
 *
 * @property int $id
 * @property int $player_id
 * @property int $player_group_id
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer wherePlayerGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PlayerGroupHasPlayer wherePlayerId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\PlayerGroup $playerGroup
 */
class PlayerGroupHasPlayer extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'player_id',
        'player_group_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerGroup()
    {
        return $this->belongsTo(PlayerGroup::class, 'player_group_id');
    }
}
