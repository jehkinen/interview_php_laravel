<?php

namespace App\Queries;

use App\Models\Character;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class CharactersQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $events = QueryBuilder::for(Character::class)
            ->allowedSorts('title')
            ->allowedFilters(
                AllowedFilter::exact('event_template_id')
            )
            ->defaultSort('title')
            ->get();

        return $events;
    }
}
