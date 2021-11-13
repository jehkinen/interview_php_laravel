<?php

namespace App\Nova;

use NovaErrorField\Errors;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\Password;
use App\Constants\NovaPermissions;
use Laravel\Nova\Http\Requests\NovaRequest;
use Vyuldashev\NovaPermission\RoleBooleanGroup;
use Vyuldashev\NovaPermission\PermissionBooleanGroup;

class User extends Resource
{
    private const PASSWORD_REGEX = 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'full_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'first_name',
        'last_name',
        'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [

            Errors::make(),
            ID::make()->sortable(),

            Gravatar::make()->maxWidth(50),

            Text::make('First name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Last name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Password::make('Password')
                ->onlyOnForms()
                ->rules('nullable', 'min:8', self::PASSWORD_REGEX),

            RoleBooleanGroup::make('Roles')
                ->canSeeWhen(NovaPermissions::ROLES_UPDATE, $this),

            PermissionBooleanGroup::make('Permissions')
                ->canSeeWhen(NovaPermissions::PERMISSIONS_UPDATE, $this),
        ];
    }

    protected static function afterValidation(NovaRequest $request, $validator)
    {
        if ($validator->errors()->getMessageBag()->has('password')) {
            $validator->errors()->add('password', 'Password should contain lower and upper case characters and one digit at least, min length 8 characters');
        }
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
