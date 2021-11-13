<?php

namespace App\Models;

use Spatie\Tags\HasTags;
use App\Traits\HasCreatedBy;
use App\Queries\EventQueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * App\Models\Event.
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $start_at
 * @property string|null $duration
 * @property string|null $color
 * @property int|null $created_by
 * @property int|null $studio_id
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \User|null $createdBy
 * @property-read \App\Models\Studio|null $studio
 * @method static \Illuminate\Database\Query\Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Event withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Event withoutTrashed()
 * @method static EventQueryBuilder|Event fetchAll()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventHasEntity[] $hasEntities
 * @property-read int|null $has_entities_count
 * @property string|null $title
 * @method static \Database\Factories\EventFactory factory(...$parameters)
 * @method static EventQueryBuilder|Event whereEventTypeId($value)
 * @method static EventQueryBuilder|Event whereTitle($value)
 * @property int $event_template_id
 * @method static EventQueryBuilder|Event whereEventTemplateId($value)
 * @property-read \App\Models\EventNote|null $note
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventNoteHasPlayerAccess[] $hasPlayerAccesses
 * @property-read int|null $has_player_accesses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Character[] $characters
 * @property-read int|null $characters_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventHasCharacter[] $hasCharacters
 * @property-read int|null $has_characters_count
 * @property-read \App\Models\EventTemplate $template
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $hasPlayers
 * @property-read int|null $has_players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PlayerGroup[] $playerGroups
 * @property-read int|null $player_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Player[] $privateNotePlayers
 * @property-read int|null $private_note_players_count
 * @property int|null $event_type_specific_person_id
 * @property \Illuminate\Database\Eloquent\Collection|\Spatie\Tags\Tag[] $tags
 * @property-read int|null $tags_count
 * @method static EventQueryBuilder|Event whereEventTypeSpecificPersonId($value)
 * @method static EventQueryBuilder|Event withAllTags($tags, ?string $type = null)
 * @method static EventQueryBuilder|Event withAllTagsOfAnyType($tags)
 * @method static EventQueryBuilder|Event withAnyTags($tags, ?string $type = null)
 * @method static EventQueryBuilder|Event withAnyTagsOfAnyType($tags)
 * @property-read \App\Models\EventTypeSpecificPerson|null $specificPerson
 * @property array|null $specific_data
 * @method static EventQueryBuilder|Event whereSpecificData($value)
 */
class Event extends BaseModel
{
    use SoftDeletes;
    use HasCreatedBy;
    use HasFactory;
    use HasTags;

    protected $casts = [
        'start_at'  => 'datetime',
        'duration' => 'datetime',
        'specific_data' => 'array',
    ];

    protected $fillable = [
        'title',
        'color',
        'duration',
        'start_at',
        'specific_data',
        'studio_id',
    ];

    public function newEloquentBuilder($query)
    {
        return new EventQueryBuilder($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasEntities() : HasMany
    {
        return $this->hasMany(EventHasEntity::class, 'event_id')
            ->where('entity_type', '!=', Character::shortClassName());
    }

    public function hasPlayers()
    {
        return $this->hasMany(Player::class, 'entity_id')->where('entity_type', Player::shortClassName());
    }

    /**
     * @return HasOne
     */
    public function note(): HasOne
    {
        return $this->hasOne(EventNote::class, 'event_id');
    }

    /**
     * @return HasMany
     */
    public function hasPlayerAccesses(): HasMany
    {
        return $this->hasMany(EventNoteHasPlayerAccess::class, 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function privateNotePlayers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Player::class,
            EventNoteHasPlayerAccess::class,
            'event_id',
            'id',
            'id',
            'player_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function players(): HasManyThrough
    {
        return $this->hasManyThrough(
            Player::class,
            EventHasEntity::class,
            'event_id',
            'id',
            'id',
            'entity_id'
        )->where('entity_type', Player::shortClassName());
    }

    public function playerGroups(): HasManyThrough
    {
        return $this->hasManyThrough(
            PlayerGroup::class,
            EventHasEntity::class,
            'event_id',
            'id',
            'id',
            'entity_id'
        )->where('entity_type', PlayerGroup::shortClassName());
    }

    /**
     * @return HasMany
     */
    public function hasCharacters(): HasMany
    {
        return $this->hasMany(EventHasCharacter::class, 'event_id');
    }

    /**
     * @return HasManyThrough
     */
    public function characters(): HasManyThrough
    {
        return $this->hasManyThrough(
            Character::class,
            EventHasCharacter::class,
            'event_id',
            'id',
            'id',
            'character_id'
        );
    }

    /**
     * @return BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EventTemplate::class, 'event_template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studio() : BelongsTo
    {
        return $this->belongsTo(Studio::class, 'studio_id');
    }

    /**
     * @return BelongsTo
     */
    public function specificPerson() : BelongsTo
    {
        return $this->belongsTo(EventTypeSpecificPerson::class, 'event_type_specific_person_id');
    }
}
