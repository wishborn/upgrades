<?php

namespace Wishborn\Upgrades\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Wishborn\Upgrades\Models\Upgrade;
use Illuminate\Support\Facades\DB;

class RunUpgradeCommand extends Command
{
    protected $signature = 'upgrade:run {--y|yes : Skip confirmations}';
    protected $description = 'Run pending upgrades';

    public function handle(): void
    {
        if (!File::exists(base_path('upgrades'))) {
            $this->error('No upgrades directory found.');
            return;
        }

        $files = collect(File::files(base_path('upgrades')))
            ->filter(fn($file) => $file->getExtension() === 'php')
            ->map(fn($file) => $file->getFilename())
            ->sort();

        $executed = Upgrade::pluck('name')->toArray();
        $pending = $files->diff($executed);

        if ($pending->isEmpty()) {
            $this->info('No pending upgrades.');
            return;
        }

        $this->info(sprintf('Found %d pending upgrades:', $pending->count()));
        $pending->each(fn($file) => $this->line('- ' . $file));

        if (!$this->option('yes') && !$this->confirm('Do you wish to run these upgrades?')) {
            return;
        }

        $batch = Upgrade::max('batch') + 1;

        foreach ($pending as $file) {
            $this->runUpgrade($file, $batch);
        }
    }

    protected function runUpgrade(string $file, int $batch): void
    {
        $this->info("Running upgrade: {$file}");

        try {
            DB::beginTransaction();

            $class = $this->getUpgradeClass($file);
            $upgrade = new $class($this);
            $upgrade->up();

            Upgrade::create([
                'name' => $file,
                'batch' => $batch,
                'executed_at' => now(),
            ]);

            DB::commit();
            $this->info("Completed upgrade: {$file}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to run upgrade {$file}: " . $e->getMessage());
            throw $e;
        }
    }

    protected function getUpgradeClass(string $file): string
    {
        $name = str_replace('.php', '', $file);
        $parts = explode('_', $name);
        array_splice($parts, 0, 4);
        $className = implode('_', $parts);
        
        require_once base_path('upgrades/' . $file);
        return '\\Upgrades\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $className)));
    }
} 