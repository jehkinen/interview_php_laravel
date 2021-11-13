<?php

namespace App\Models;

use App\Queries\CharactersQueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Character.
 *
 * @property int $id
 * @property string $title
 * @property int $event_template_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EventTemplate $eventTemplate
 * @method static \Illuminate\Database\Eloquent\Builder|Character newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Character newQuery()
 * @method static \Illuminate\Database\Query\Builder|Character onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Character query()
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereEventTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Character whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Character withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Character withoutTrashed()
 * @mixin \Eloquent
 * @method static CharactersQueryBuilder|Character fetchAll()
 * @property string|null $duration
 * @property int $rpe
 * @method static CharactersQueryBuilder|Character whereDuration($value)
 * @method static CharactersQueryBuilder|Character whereRpe($value)
 */
class Character extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    public $attributes = [
        'rpe' => 0,
    ];

    protected $fillable = [
        'event_template_id',
        'title',
        'rpe',
        'duration',
    ];

    public function newEloquentBuilder($query)
    {
        return new CharactersQueryBuilder($query);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function eventTemplate()
    {
        return $this->belongsTo(EventTemplate::class, 'event_template_id');
    }

    /**
     * @param $searchString
     */
    public static function ajaxMultiselectSearchQuery($searchString)
    {
        return self::query()
            ->where('title', 'like', '%' . $searchString . '%');
    }
}
