<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * App\Models\EventHasEntity.
 *
 * @property-read \App\Models\Event $event
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity query()
 * @mixin \Eloquent
 * @property-read Model|\Eloquent $entity
 * @property int $id
 * @property int $event_id
 * @property string $entity_type
 * @property int $entity_id
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventHasEntity whereId($value)
 * @property-read Model|\Eloquent $model
 */
class EventHasEntity extends BaseModel
{
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'entity_id',
        'entity_type',
        'model_id',
        'model_type',
    ];

    public static function boot()
    {
        parent::boot();

        Relation::morphMap([
            Studio::shortClassName() => Studio::class,
            PlayerGroup::shortClassName() => PlayerGroup::class,
            User::shortClassName() => User::class,
            Character::shortClassName() => Character::class,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }

    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
