<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventTemplateHasPlayerGroup.
 *
 * @property int $id
 * @property int $event_template_id
 * @property int $player_group_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup whereEventTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup wherePlayerGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTemplateHasPlayerGroup whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\EventTemplate $eventTemplate
 * @property-read \App\Models\PlayerGroup $playerGroup
 */
class EventTemplateHasPlayerGroup extends BaseModel
{
    use HasFactory;

    public $fillable = [
        'event_template_id',
        'player_group_id',
    ];

    /**
     * @return BelongsTo
     */
    public function eventTemplate() : BelongsTo
    {
        return $this->belongsTo(EventTemplate::class, 'event_template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerGroup() : BelongsTo
    {
        return $this->belongsTo(PlayerGroup::class, 'player_group_id');
    }
}
