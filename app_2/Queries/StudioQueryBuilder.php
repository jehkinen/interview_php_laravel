<?php

namespace App\Queries;

use App\Models\Studio;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class StudioQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(Studio::class)
            ->allowedSorts('title')
            ->where('is_active', true)
            ->allowedFilters([
                AllowedFilter::partial('title'),
            ])
            ->defaultSort('title')
            ->get();

        return $events;
    }
}
