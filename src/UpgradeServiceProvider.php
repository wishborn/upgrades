<?php

namespace Wishborn\Upgrades;

use Illuminate\Support\ServiceProvider;
use Wishborn\Upgrades\Console\Commands\MakeUpgradeCommand;
use Wishborn\Upgrades\Console\Commands\RunUpgradeCommand;
use Wishborn\Upgrades\Console\Commands\UpgradeStatusCommand;

class UpgradeServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeUpgradeCommand::class,
                RunUpgradeCommand::class,
                UpgradeStatusCommand::class,
            ]);

            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

            $this->publishes([
                __DIR__ . '/database/migrations' => database_path('migrations'),
            ], 'wishborn-upgrades-migrations');
        }
    }

    public function register(): void
    {
        //
    }
} 