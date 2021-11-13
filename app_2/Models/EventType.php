<?php

namespace App\Models;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventType.
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EventTemplate[] $eventTemplates
 * @property-read int|null $event_templates_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|EventType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventType withoutTrashed()
 * @mixin \Eloquent
 * @property bool $is_active
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereIsActive($value)
 * @property int $item_order
 * @method static \Illuminate\Database\Eloquent\Builder|EventType ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereItemOrder($value)
 * @property mixed|null $specific_labels
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereSpecificLabels($value)
 */
class EventType extends BaseModel implements Sortable
{
    use HasFactory;
    use SoftDeletes;
    use SortableTrait;

    public $attributes = [
        'item_order' => 1,
    ];

    public $sortable = [
        'order_column_name' => 'item_order',
        'sort_when_creating' => true,
    ];

    protected $casts = [
        'specific_labels' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function eventTemplates() : HasMany
    {
        return $this->hasMany(EventTemplate::class, 'event_type_id');
    }
}
