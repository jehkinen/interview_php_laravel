<?php

namespace App\Models;

use App\Queries\PlayerGroupQueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\PlayerGroup.
 *
 * @property int $id
 * @property mixed|null $players_json
 * @property string|null $title Group title
 * @property string|null $description Group description
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $players
 * @property-read int|null $players_count
 * @method static PlayerGroupQueryBuilder|PlayerGroup fetchAll()
 * @method static PlayerGroupQueryBuilder|PlayerGroup newModelQuery()
 * @method static PlayerGroupQueryBuilder|PlayerGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|PlayerGroup onlyTrashed()
 * @method static PlayerGroupQueryBuilder|PlayerGroup query()
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereCreatedAt($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereCreatedBy($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereDeletedAt($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereDescription($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereId($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup wherePlayersJson($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereTitle($value)
 * @method static PlayerGroupQueryBuilder|PlayerGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|PlayerGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|PlayerGroup withoutTrashed()
 * @mixin \Eloquent
 * @method static \Database\Factories\PlayerGroupFactory factory(...$parameters)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlayerGroupHasPlayer[] $playerGroupHasPlayers
 * @property-read int|null $player_group_has_players_count
 * @method static PlayerGroupQueryBuilder|PlayerGroup ajaxMultiselectSearchQuery($searchString)
 * @method static PlayerGroupQueryBuilder|PlayerGroup getAjaxMultiselectOptions($value)
 */
class PlayerGroup extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = [
        'title',
        'description',
    ];

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PlayerGroupQueryBuilder|\Illuminate\Database\Eloquent\Builder
     */
    public function newEloquentBuilder($query)
    {
        return new PlayerGroupQueryBuilder($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playerGroupHasPlayers()
    {
        return $this->hasMany(PlayerGroupHasPlayer::class, 'player_group_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function players()
    {
        return $this->hasManyThrough(
            Player::class,
            PlayerGroupHasPlayer::class,
            'player_group_id',
            'id',
            'id',
            'player_id'
        );
    }
}
