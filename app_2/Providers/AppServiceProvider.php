<?php

namespace App\Providers;

use App\Models\Team;
use Laravel\Nova\Nova;
use App\Models\Character;
use App\Models\EventType;
use App\Models\PlayerGroup;
use App\Models\EventTemplate;
use Illuminate\Support\ServiceProvider;
use App\Nova\Observers\NovaTeamObserver;
use App\Nova\Observers\NovaCharacterObserver;
use App\Nova\Observers\NovaEventTypeObserver;
use App\Nova\Observers\NovaPlayerGroupObserver;
use App\Nova\Observers\NovaEventTemplateObserver;
use Laravel\Telescope\TelescopeServiceProvider as BaseTelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(BaseTelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Nova::serving(function () {
            EventTemplate::observe(NovaEventTemplateObserver::class);
            Character::observe(NovaCharacterObserver::class);
            PlayerGroup::observe(NovaPlayerGroupObserver::class);
            Team::observe(NovaTeamObserver::class);
            EventType::observe(NovaEventTypeObserver::class);
        });
    }
}
