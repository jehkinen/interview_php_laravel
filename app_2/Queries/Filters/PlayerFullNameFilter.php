<?php

namespace App\Queries\Filters;

use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PlayerFullNameFilter implements Filter
{
    public function __invoke(Builder $query, $searchString, string $property): Builder
    {
        $searchString = Str::lower($searchString);

        $query->where(function ($q) use ($searchString) {
            $q->where('first_name', 'LIKE', '%' . $searchString . '%');
            $q->orWhere('last_name', 'LIKE', '%' . $searchString . '%');
        });

        return $query;
    }
}
