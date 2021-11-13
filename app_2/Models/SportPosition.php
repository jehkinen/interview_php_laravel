<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SportPosition.
 *
 * @property int $id
 * @property string $label
 * @property int $sport_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Sport $sport
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition query()
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition whereSportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SportPosition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SportPosition extends BaseModel
{
    use HasFactory;

    public $with = [
        'sport',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }
}
