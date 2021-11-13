<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\Timezone.
 *
 * @property int $id
 * @property int $is_active
 * @property string $title
 * @property string $offset
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone newQuery()
 * @method static \Illuminate\Database\Query\Builder|Timezone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereOffset($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timezone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Timezone withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Timezone withoutTrashed()
 * @mixin \Eloquent
 */
class Timezone extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'offset',
    ];
}
