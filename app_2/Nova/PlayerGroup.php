<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use App\Nova\Fields\ItemsCount;
use Laravel\Nova\Fields\Textarea;
use App\Models\Player as PlayerModel;
use Bessamu\AjaxMultiselectNovaField\AjaxMultiselect;

class PlayerGroup extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\PlayerGroup::class;

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
            Text::make('Group Name', 'title')->sortable()->rules('required', 'max:100', 'min:5'),
            Textarea::make('Description')
                ->onlyOnForms()
                ->sortable(),

            ItemsCount::makeField('Group Participants', $this->players()->count()),

            AjaxMultiselect::make('Group Participants', 'players_json')
                ->optionsModel(PlayerModel::class)
                ->optionsLabel('full_name')
                ->placeholder('Select Participants')
                ->showOnIndex(false)
                ->maxOptions(100)
                ->rules('required'),
        ];
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
