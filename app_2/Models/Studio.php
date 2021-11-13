<?php

namespace App\Models;

use App\Traits\HasCreatedBy;
use App\Queries\StudioQueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Studio.
 *
 * @property int $id
 * @property string $title Studio title
 * @property string|null $description Studio description
 * @property int|null $created_by
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Studio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Studio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Studio query()
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \User|null $createdBy
 * @method static \Illuminate\Database\Query\Builder|Studio onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Studio withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Studio withoutTrashed()
 * @method static \Database\Factories\StudioFactory factory(...$parameters)
 * @property int|null $capacity
 * @method static \Illuminate\Database\Eloquent\Builder|Studio whereCapacity($value)
 * @method static StudioQueryBuilder|Studio fetchAll()
 * @property int $is_active
 * @method static StudioQueryBuilder|Studio whereIsActive($value)
 * @property int|null $time_zone_id
 * @property string|null $city
 * @method static StudioQueryBuilder|Studio whereCity($value)
 * @method static StudioQueryBuilder|Studio whereTimeZoneId($value)
 */
class Studio extends BaseModel
{
    use HasCreatedBy;
    use SoftDeletes;
    use HasFactory;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'title',
        'description',
        'capacity',
        'time_zone_id',
        'city',
    ];

    public function newEloquentBuilder($query)
    {
        return new StudioQueryBuilder($query);
    }
}
