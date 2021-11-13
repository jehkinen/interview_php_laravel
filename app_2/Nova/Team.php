<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use App\Models\Player as PlayerModel;
use Bessamu\AjaxMultiselectNovaField\AjaxMultiselect;

class Team extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Team::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'title',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make('Title'),

            Text::make('Players', function () {
                return $this->players()
                    ->selectRaw('players.*, concat(first_name, " " , last_name) as full_name')
                    ->pluck('full_name')->implode(', ');
            }),

            AjaxMultiselect::make('Players', 'players_json')
                ->optionsModel(PlayerModel::class)
                ->optionsLabel('full_name')
                ->placeholder('Select Player')
                ->onlyOnForms()
                ->rules(['required'])
                ->maxOptions(100),
        ];
    }

    public function getPlayersList()
    {
        return $this
            ->players()
            ->selectRaw('players.id, concat(first_name, " ", last_name ) as title')
            ->get()
            ->pluck('players.id', 'title')
            ->all();
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
