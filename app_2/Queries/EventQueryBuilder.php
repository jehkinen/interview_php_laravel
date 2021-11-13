<?php

namespace App\Queries;

use App\Models\Event;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class EventQueryBuilder extends Builder
{
    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Spatie\QueryBuilder\Concerns\SortsQuery[]|QueryBuilder[]
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(Event::class)
            ->with([
                'hasEntities',
                'note',
                'players.sportPosition.sport',
                'hasCharacters.player.sportPosition.sport',
                'hasCharacters.character',
                'hasPlayerAccesses.player',
            ])
            ->allowedFilters([
                AllowedFilter::exact('studio_id'),
            ])
            ->defaultSort('-created_at')
            ->get();

        return $events;
    }
}
