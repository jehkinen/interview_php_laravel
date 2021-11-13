<?php

namespace App\Nova;

use App\Models\Timezone;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Textarea;
use Davidpiesse\NovaToggle\Toggle;

class Studio extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Studio::class;

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
            Text::make('Studio/Theatre name', 'title')->sortable(),
            Text::make('Capacity')->sortable(),
            Textarea::make('Description')
                ->onlyOnForms()
                ->sortable(),

            Toggle::make('Active', 'is_active')
                ->trueLabel('Active')
                ->falseLabel('Inactive'),

            Select::make('Timezone', 'time_zone_id')->options($this->getTimeZoneList()),
            Text::make('City'),

        ];
    }

    protected function getTimeZoneList()
    {
        return Timezone::query()
            ->where('is_active', true)
            ->pluck('title', 'id')
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
