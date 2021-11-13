<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Spatie\TagsField\Http\Middleware\Authorize;

class TagsFieldServiceProvider extends \Spatie\TagsField\TagsFieldServiceProvider
{
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['web', Authorize::class])
            ->prefix('nova-vendor/spatie/nova-tags-field')
            ->group(base_path() . '/vendor/spatie/nova-tags-field/routes/api.php');
    }
}
