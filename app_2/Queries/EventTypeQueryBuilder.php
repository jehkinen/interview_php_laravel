<?php

namespace App\Queries;

use App\Models\EventTemplate;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class EventTypeQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(EventTemplate::class)
            ->with('playerGroups.players')
            ->defaultSort('title')
            ->get();

        return $events;
    }
}
