<?php

namespace Wishborn\Upgrades\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Wishborn\Upgrades\Models\Upgrade;

class UpgradeStatusCommand extends Command
{
    protected $signature = 'wish:upgrade-status';
    protected $description = 'Show the status of all upgrades';

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

        $executed = Upgrade::all()->keyBy('name');

        $headers = ['Upgrade', 'Batch', 'Executed At', 'Status'];
        $rows = [];

        foreach ($files as $file) {
            $upgrade = $executed->get($file);
            $rows[] = [
                $file,
                $upgrade ? $upgrade->batch : '-',
                $upgrade ? $upgrade->executed_at->format('Y-m-d H:i:s') : '-',
                $upgrade ? '<info>Completed</info>' : '<comment>Pending</comment>'
            ];
        }

        $this->table($headers, $rows);

        $pendingCount = $files->count() - $executed->count();
        if ($pendingCount > 0) {
            $this->info("\nYou have {$pendingCount} pending upgrades.");
        } else {
            $this->info("\nAll upgrades have been executed.");
        }
    }
} 