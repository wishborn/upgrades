<?php

namespace Wishborn\Upgrades\Support;

use Illuminate\Console\Command;

abstract class Upgrade
{
    protected Command $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    abstract public function up(): void;

    protected function info(string $message): void
    {
        $this->command->info($message);
    }

    protected function error(string $message): void
    {
        $this->command->error($message);
    }

    protected function warn(string $message): void
    {
        $this->command->warn($message);
    }

    protected function comment(string $message): void
    {
        $this->command->comment($message);
    }

    protected function line(string $message): void
    {
        $this->command->line($message);
    }

    protected function newLine(int $count = 1): void
    {
        $this->command->newLine($count);
    }

    protected function confirm(string $question, bool $default = false): bool
    {
        return $this->command->confirm($question, $default);
    }
} 