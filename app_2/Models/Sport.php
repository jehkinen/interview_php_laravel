<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Sport.
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Sport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sport newQuery()
 * @method static \Illuminate\Database\Query\Builder|Sport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sport query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sport whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Sport withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Sport withoutTrashed()
 * @mixin \Eloquent
 */
class Sport extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'title',
    ];
}
