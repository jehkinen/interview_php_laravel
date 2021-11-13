<?php

namespace App\Traits;

use Carbon\Carbon;

trait UsedAtTrait
{
    /**
     * @return bool
     */
    public function markAsUsed()
    {
        $this->used_at = Carbon::now();

        return $this->save();
    }
}
