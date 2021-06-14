<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SupplierSchedule
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $started_at
 * @property string $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Supplier $supplier
 * @method static \Database\Factories\SupplierScheduleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereSupplierId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupplierSchedule whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SupplierSchedule extends Model
{
    use HasFactory;

    public $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
