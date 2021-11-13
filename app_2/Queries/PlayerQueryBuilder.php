<?php

namespace App\Queries;

use App\Models\Player;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Queries\Filters\PlayerFullNameFilter;
use App\Queries\Filters\PlayerSortByFullName;

class PlayerQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $models = QueryBuilder::for(Player::class)
            ->allowedSorts(AllowedSort::custom('title', new PlayerSortByFullName()))
            ->allowedFilters(
                AllowedFilter::exact('sport_position_id'),
                AllowedFilter::custom('full_name', new PlayerFullNameFilter())
            )
            ->defaultSort('-created_at')
            ->get();

        return $models;
    }
}
