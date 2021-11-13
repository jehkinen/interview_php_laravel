<?php

namespace App\Nova;

use App\Models\Character;
use Spatie\TagsField\Tags;
use App\Models\PlayerGroup;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Yna\NovaSwatches\Swatches;
use App\Nova\Fields\ItemsCount;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use App\Nova\Filters\EventTypeFilter;
use Laraning\NovaTimeField\TimeField;
use Naxon\NovaFieldSortable\Sortable;
use OptimistDigital\MultiselectField\Multiselect;
use Bessamu\AjaxMultiselectNovaField\AjaxMultiselect;
use Naxon\NovaFieldSortable\Concerns\SortsIndexEntries;

class EventTemplate extends BaseResource
{
    use SortsIndexEntries;

    public static $defaultSortField = 'item_order';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\EventTemplate::class;

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

            Sortable::make('Item Order', 'id')->onlyOnIndex(),

            Text::make('Title')->rules('required', 'min:5', 'max:100'),
            Text::make('Order', 'item_order')->onlyOnIndex(),

            //@todo need to fix this bug, not sure which bug it was?
            BelongsTo::make('Event Type', 'eventType')
                ->withMeta([
                    'belongsToId' => $this->queryParams->get('event_type_id') ?? $this->event_type_id,
                ])
                ->withoutTrashed()
                ->rules('required'),

            TimeField::make('Duration', 'duration'),

            HasMany::make('Characters', 'characters')

                ->onlyOnDetail(),

            Tags::make('Performance', 'tags')
                ->single(),

            AjaxMultiselect::make('Players Groups', 'player_groups_json')
                ->optionsModel(PlayerGroup::class)
                ->optionsLabel('title')
                ->placeholder('Select Player Group')
                ->showOnIndex(false)
                ->default(function () {
                    return json_encode([]);
                })
                ->maxOptions(100),

            Multiselect::make('Characters', 'characters_json')
                ->max(10)
                ->nullable()
                ->options($this->getCharactersList())
                ->exceptOnForms()

                ->saveAsJSON(),

            ItemsCount::makeField('Player Groups', $this->playerGroups()->count()),

            Swatches::make('Color', 'color')
                ->rules('required'),
        ];
    }

    private function getCharactersList()
    {
        return Character::query()
            ->select('id', 'title')
            ->where('event_template_id', $this->id)
            ->get()
            ->pluck('title', 'id');
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
        return [
            new EventTypeFilter(),
        ];
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
