<?php

namespace Wishborn\Upgrades\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Wishborn\Upgrades\Models\Upgrade;

class RollbackUpgradeCommand extends Command
{
    protected $signature = 'wish:upgrade:rollback 
        {--step=1 : The number of upgrade batches to rollback}
        {--y|yes : Skip confirmations}';

    protected $description = 'Rollback the last upgrade batch or a specific number of batches';

    public function handle(): void
    {
        $step = (int) $this->option('step');
        
        // Get the upgrades to rollback
        $upgrades = Upgrade::query()
            ->orderBy('batch', 'desc')
            ->orderBy('executed_at', 'desc')
            ->take($step)
            ->get()
            ->groupBy('batch');

        if ($upgrades->isEmpty()) {
            $this->info('Nothing to rollback.');
            return;
        }

        $this->info(sprintf('Rolling back %d %s:', 
            $upgrades->count(), 
            str('batch')->plural($upgrades->count())
        ));

        foreach ($upgrades as $batch => $batchUpgrades) {
            $this->info(sprintf('Batch %d:', $batch));
            foreach ($batchUpgrades as $upgrade) {
                $this->line('- ' . $upgrade->name);
            }
        }

        if (!$this->option('yes') && !$this->confirm('Do you wish to rollback these upgrades?')) {
            return;
        }

        // Rollback each upgrade in reverse order
        foreach ($upgrades->flatten() as $upgrade) {
            $this->rollbackUpgrade($upgrade);
        }
    }

    protected function rollbackUpgrade(Upgrade $upgrade): void
    {
        $this->info("Rolling back upgrade: {$upgrade->name}");

        try {
            $class = $this->getUpgradeClass($upgrade->name);
            $upgradeInstance = new $class($this);
            
            if (!method_exists($upgradeInstance, 'down')) {
                $this->warn("Upgrade {$upgrade->name} does not have a down method. Skipping rollback but removing from history.");
            } else {
                // Execute the rollback
                $upgradeInstance->down();
            }

            // Remove the upgrade record
            $upgrade->delete();

            $this->info("Completed rollback: {$upgrade->name}");
        } catch (\Exception $e) {
            $this->error("Failed to rollback upgrade {$upgrade->name}: " . $e->getMessage());
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
        return '\\Wishborn\\Upgrades\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $className)));
    }
} 