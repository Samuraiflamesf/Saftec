<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Policies\ActivityPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Coolify
        if ($this->app->environment('production')) {
            URL::forceRootUrl(config('app.url'));
        }

        Gate::policy(Activity::class, ActivityPolicy::class);

    }
}
