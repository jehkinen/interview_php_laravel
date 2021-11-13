<?php

namespace App\Providers;

use Laravel\Nova\Nova;
use App\Constants\NovaRoles;
use Illuminate\Support\Facades\Gate;
use App\Nova\Policies\NovaTeamPolicy;
use App\Nova\Policies\NovaUserPolicy;
use App\Nova\Policies\NovaSportPolicy;
use App\Nova\Policies\NovaPlayerPolicy;
use App\Nova\Policies\NovaStudioPolicy;
use App\Policies\NovaPerformancePolicy;
use App\Nova\Policies\NovaCharacterPolicy;
use App\Nova\Policies\NovaEventTypePolicy;
use App\Nova\Policies\NovaPermissionPolicy;
use App\Nova\Policies\NovaEventTemplatePolicy;
use App\Nova\Policies\NovaSportPositionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    protected $novaPolicies = [
        NovaCharacterPolicy::class,
        NovaEventTemplatePolicy::class,
        NovaPlayerPolicy::class,
        NovaSportPolicy::class,
        NovaSportPositionPolicy::class,
        NovaStudioPolicy::class,
        NovaTeamPolicy::class,
        NovaUserPolicy::class,
        NovaPermissionPolicy::class,
        NovaEventTypePolicy::class,
        NovaPerformancePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before(
            function ($user, $ability) {
                return $user->hasRole(NovaRoles::ADMIN, 'web') ? true : null;
            }
        );

        $this->registerPolicies();
        Nova::serving(function () {
            $this->registerNovaPolicies();
        });
    }

    public function registerNovaPolicies()
    {
        foreach ($this->novaPolicies as $policyClass) {
            if (! $policyClass::$model) {
                throw new \ErrorException('Please specify policy model for policy ' . $policyClass);
            }
            Gate::policy($policyClass::$model, $policyClass);
        }
    }
}
