<?php

namespace App\Queries\Filters;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class PlayerSortByFullName implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        if ($descending) {
            $query->orderByRaw('concat(first_name, " ", last_name) desc');
        } else {
            $query->orderByRaw('concat(first_name, " ", last_name) asc');
        }

        return $query;
    }
}
