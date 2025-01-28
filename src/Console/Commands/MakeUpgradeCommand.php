<?php

namespace Wishborn\Upgrades\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MakeUpgradeCommand extends Command
{
    protected $signature = 'wish:make-upgrade {name? : The name of the upgrade}';
    protected $description = 'Create a new upgrade file';

    protected $aliases = ['make:wish-upgrade'];

    public function handle(): void
    {
        $name = $this->argument('name') ?? $this->ask('What is the upgrade description?');
        $name = Str::studly($name);
        
        $timestamp = now()->format('Y_m_d_His');
        $filename = $timestamp . '_' . Str::snake($name) . '.php';
        
        $path = base_path('upgrades');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        
        $stub = File::get(__DIR__ . '/../../stubs/upgrade.stub');
        $stub = str_replace('DummyClass', $name, $stub);
        
        File::put($path . '/' . $filename, $stub);
        
        $this->info('Upgrade created successfully: ' . $filename);
    }
} 