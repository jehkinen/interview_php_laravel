<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use Illuminate\Support\Facades\Date;
use Spatie\EloquentSortable\Sortable;
use App\Queries\EventTemplateQueryBuilder;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\EventType.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate query()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $duration
 * @property string $title
 * @property string $color
 * @property string $event_type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Database\Factories\EventTemplateFactory factory(...$parameters)
 * @method static \Illuminate\Database\Query\Builder|EventTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventTemplate withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTemplate withoutTrashed()
 * @method static EventTemplateQueryBuilder|EventTemplate fetchAll()
 * @method static EventTemplateQueryBuilder|EventTemplate whereTemplateType($value)
 * @property mixed|null $characters_json
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Character[] $characters
 * @property-read int|null $characters_count
 * @method static EventTemplateQueryBuilder|EventTemplate whereCharactersJson($value)
 * @property mixed|null $player_groups_json
 * @method static EventTemplateQueryBuilder|EventTemplate wherePlayerGroupsJson($value)
 * @property-read mixed $player_groups_as_json
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventTemplateHasPlayerGroup[] $hasPlayerGroups
 * @property-read int|null $has_player_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlayerGroup[] $playerGroups
 * @property-read int|null $player_groups_count
 * @property int|null $event_type_id
 * @property-read \App\Models\EventType|null $eventType
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static EventTemplateQueryBuilder|EventTemplate whereEventTypeId($value)
 * @method static EventTemplateQueryBuilder|EventTemplate withAllTags($tags, ?string $type = null)
 * @method static EventTemplateQueryBuilder|EventTemplate withAllTagsOfAnyType($tags)
 * @method static EventTemplateQueryBuilder|EventTemplate withAnyTags($tags, ?string $type = null)
 * @method static EventTemplateQueryBuilder|EventTemplate withAnyTagsOfAnyType($tags)
 * @property int|null $item_order
 * @method static EventTemplateQueryBuilder|EventTemplate ordered(string $direction = 'asc')
 * @method static EventTemplateQueryBuilder|EventTemplate whereItemOrder($value)
 * @property-read mixed $duration_in_hour
 */
class EventTemplate extends BaseModel implements Sortable
{
    use SortableTrait;
    use HasFactory;
    use SoftDeletes;
    use HasTags;

    public $sortable = [
        'order_column_name' => 'item_order',
        'sort_when_creating' => true,
    ];

    protected $attributes = [
        'duration' => '01:00:00',
    ];

    protected $casts = [
        'characters_json' => 'array',
    ];

    protected $fillable = [
        'color',
        'duration',
        'title',
        'event_type_id',
        'item_order',
    ];

    public function newEloquentBuilder($query)
    {
        return new EventTemplateQueryBuilder($query);
    }

    public function getDurationInHourAttribute()
    {
        if ($this->duration) {
            $date = Date::createFromFormat('H:m:i', $this->duration);

            return $date->format('i') / 60 + (int) $date->format('H');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'event_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function characters()
    {
        return $this->hasMany(Character::class, 'event_template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasPlayerGroups(): HasMany
    {
        return $this->hasMany(EventTemplateHasPlayerGroup::class, 'event_template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function playerGroups(): HasManyThrough
    {
        return $this->hasManyThrough(
            PlayerGroup::class,
            EventTemplateHasPlayerGroup::class,
            'event_template_id',
            'id',
            'id',
            'player_group_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function eventType() : BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function buildSortQuery()
    {
        return static::query()->where('event_type_id', $this->event_type_id);
    }
}
