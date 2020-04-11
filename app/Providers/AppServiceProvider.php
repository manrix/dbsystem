<?php

namespace DBSystem\Providers;

use DBSystem\Backup;
use DBSystem\Database;
use DBSystem\Destination;
use DBSystem\Log;
use DBSystem\Observers\BackupObserver;
use DBSystem\Observers\DatabaseObserver;
use DBSystem\Observers\DestinationObserver;
use DBSystem\Observers\TaskLogObserver;
use DBSystem\Settings;
use DBSystem\Task;
use DBSystem\Observers\TaskObserver;
use DBSystem\TaskLog;
use DBSystem\Tasks\Logger;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Task::observe(TaskObserver::class);
        Backup::observe(BackupObserver::class);
        TaskLog::observe(TaskLogObserver::class);
        Database::observe(DatabaseObserver::class);
        Destination::observe(DestinationObserver::class);

        $this->app->bind(Logger::class, function ($app) {
            return new Logger(new Log());
        });

        $this->app->singleton('settings', function ($app) {
            return cache()->remember('app.settings', 60, function () {
                return Settings::all()->pluck('value', 'key')->toArray();
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
