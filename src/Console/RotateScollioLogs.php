<?php

namespace Kz370\ScollioLogger\Console;

use Illuminate\Console\Command;
use Kz370\ScollioLogger\Models\ScollioLogger;

class RotateScollioLogs extends Command
{
    protected $signature = 'scollio:rotate';
    protected $description = 'Delete logs older than retention_days from scollio_logger table';

    public function handle(): int
    {
        $days = (int) config('scollio-logger.retention_days', 0);
        if ($days <= 0) {
            $this->info('Rotation disabled (retention_days <= 0).');
            return self::SUCCESS;
        }

        $cutoff = now()->subDays($days);

        $count = ScollioLogger::where(function($q) use ($cutoff) {
            $q->whereNotNull('requested_at')->where('requested_at', '<', $cutoff)
              ->orWhereNull('requested_at')->where('created_at', '<', $cutoff);
        })->delete();

        $this->info("Rotated {$count} records older than {$days} days.");
        return self::SUCCESS;
    }
}
