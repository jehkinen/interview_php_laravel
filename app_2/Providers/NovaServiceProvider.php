<?php

namespace App\Providers;

use App\Models\User;
use Laravel\Nova\Nova;
use App\Constants\NovaRoles;
use Laravel\Nova\Cards\Help;
use App\Constants\NovaPermissions;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Events\ServingNova;
use App\Nova\Policies\NovaRolePolicy;
use GeneaLabs\NovaTelescope\NovaTelescope;
use App\Nova\Policies\NovaPermissionPolicy;
use Laravel\Nova\NovaApplicationServiceProvider;
use Vyuldashev\NovaPermission\NovaPermissionTool;
use Laravel\Nova\Http\Controllers\ResetPasswordController;
use Bessamu\AjaxMultiselectNovaField\Http\Controllers\AjaxMultiselectController;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(
            function (ServingNova $event) {
                $this->authorization();

                Nova::style('admin', public_path('css/vue-multiselect.min.css'));
                Nova::style('admin', public_path('css/nova-b54.css'));
            }
        );
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define(
            'viewNova',
            function (User $user) {
                return $user->hasRole(NovaRoles::ADMIN) || $user->hasPermissionTo(NovaPermissions::NOVA_ACCESS);
            }
        );
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new Help,
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        $tools = [];
        if (! App::environment('production')) {
            $tools[] = new NovaTelescope();
        }
        $tools[] =
            NovaPermissionTool::make()
            ->rolePolicy(NovaRolePolicy::class)
            ->permissionPolicy(NovaPermissionPolicy::class);

        return $tools;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AjaxMultiselectController::class, \App\Http\Controllers\Nova\AjaxMultiselectController::class);
        $this->app->bind(ResetPasswordController::class, \App\Http\Controllers\Nova\ResetPasswordController::class);
    }
}
