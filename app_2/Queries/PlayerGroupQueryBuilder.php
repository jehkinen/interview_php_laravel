<?php

namespace App\Queries;

use App\Models\PlayerGroup;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class PlayerGroupQueryBuilder extends Builder
{
    /**
     * @return Collection
     */
    public function fetchAll()
    {
        $models = QueryBuilder::for(PlayerGroup::class)
            ->allowedFilters([
                AllowedFilter::partial('title'),
            ])
            ->defaultSort('-created_at')
            ->get();

        return $models;
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function getAjaxMultiselectOptions($value)
    {
        return PlayerGroup::query()
            ->whereIn('id', json_decode($value))
            ->get();
    }

    /**
     * @param $searchString
     */
    public static function ajaxMultiselectSearchQuery($searchString)
    {
        return PlayerGroup::query()->where('title', 'like', '%' . $searchString . '%');
    }
}
