<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Queries\EventTypeSpecificPersonQueryBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\EventTypeSpecificPerson.
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $title
 * @property int $event_type_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeSpecificPerson whereUpdatedAt($value)
 * @method static EventTypeSpecificPersonQueryBuilder|EventTypeSpecificPerson fetchAll()
 */
class EventTypeSpecificPerson extends Model
{
    use HasFactory;

    public function newEloquentBuilder($query)
    {
        return new EventTypeSpecificPersonQueryBuilder($query);
    }

    protected $fillable = [
        'event_type_id',
        'title',
    ];
}
