<?php

namespace Wishborn\Upgrades\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Wishborn\Upgrades\UpgradeServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loadMigrationsFrom(__DIR__ . '/../src/database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            UpgradeServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set up database configuration
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
} 