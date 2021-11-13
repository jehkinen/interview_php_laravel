<?php

namespace App\Queries;

use App\Models\EventTemplate;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class EventTemplateQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(EventTemplate::class)
            ->allowedSorts('title')
            ->allowedFilters(
                AllowedFilter::exact('event_type_id'),
                AllowedFilter::partial('title')
            )
            ->allowedSorts('title', 'item_order')
            ->whereHas('eventType', function ($q) {
                $q->where('is_active', true);
            })
            ->with(['eventType', 'characters', 'playerGroups.players'])
            ->orderByRaw('event_type_id, item_order asc')
            ->get();

        return $events;
    }
}
