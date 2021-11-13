<?php

namespace App\Queries;

use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\EventTypeSpecificPerson;
use Illuminate\Database\Eloquent\Builder;

class EventTypeSpecificPersonQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(EventTypeSpecificPerson::class)
            ->allowedFilters(
                AllowedFilter::exact('event_type_id'),
                AllowedFilter::partial('title')
            )
            ->get();

        return $events;
    }
}
