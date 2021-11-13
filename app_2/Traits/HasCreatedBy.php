<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class HasCreatedBy.
 * @mixin Model
 */
trait HasCreatedBy
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
