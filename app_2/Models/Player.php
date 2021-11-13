<?php

namespace App\Models;

use App\Queries\PlayerQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Player.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int|null $sport_position_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\SportPosition|null $sportPosition
 * @method static \Illuminate\Database\Eloquent\Builder|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereSportPositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Player whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Team[] $teams
 * @property-read int|null $teams_count
 * @property-read string $full_name
 * @method static PlayerQueryBuilder|Player fetchAll()
 * @property int $user_id
 * @property-read \App\Models\User $user
 * @method static PlayerQueryBuilder|Player whereUserId($value)
 */
class Player extends BaseModel
{
    protected $fillable = [
        'first_name',
        'last_name',
        'position_id',
        'user_id'
    ];

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return PlayerQueryBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new PlayerQueryBuilder($query);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sportPosition()
    {
        return $this->belongsTo(SportPosition::class, 'sport_position_id');
    }

    /**
     * @return HasManyThrough
     */
    public function teams()
    {
        return $this->hasManyThrough(
            Team::class,
            TeamPlayer::class,
            'player_id',
            'id',
            'id',
            'team_id'
        );
    }

    /**
     * @param $value
     * @return Player[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAjaxMultiselectOptions($value)
    {
        return Player::query()
            ->selectRaw('players.*, concat(first_name, " " , last_name) as full_name')
            ->whereIn('id', json_decode($value))
            ->get();
    }

    /**
     * @param $searchString
     */
    public static function ajaxMultiselectSearchQuery($searchString)
    {
        return Player::query()
            ->selectRaw('players.*, concat(first_name, " " , last_name) as full_name')
            ->where('first_name', 'like', '%' . $searchString . '%')
            ->orWhere('last_name', 'like', '%' . $searchString . '%')
            ->orderByRaw('first_name, last_name ASC');
    }
}
