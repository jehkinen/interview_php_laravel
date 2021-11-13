<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Team.
 *
 * @property int $id
 * @property string $title
 * @property int $is_club
 * @property int|null $team_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Query\Builder|Team onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereIsClub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Team withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Team withoutTrashed()
 * @mixin \Eloquent
 * @property array|null $players_json
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TeamPlayer[] $teamPlayers
 * @property-read int|null $team_players_count
 * @method static \Illuminate\Database\Eloquent\Builder|Team wherePlayersJson($value)
 */
class Team extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'is_club',
        'title',
        'team_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teamPlayers()
    {
        return $this->hasMany(TeamPlayer::class, 'team_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function players()
    {
        return $this->hasManyThrough(
            Player::class,
            TeamPlayer::class,
            'team_id',
            'id',
            'id',
            'player_id'
        );
    }
}
