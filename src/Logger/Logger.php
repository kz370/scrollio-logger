<?php
namespace Scollio\Logger;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Scollio\Models\LogEntry;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Carbon\Carbon;

class Logger implements LoggerInterface
{
    public function log($level, $message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $trace  = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1] ?? [];

        $resolvedLocation = $location ?? ($context['location'] ?? (($caller['class'] ?? '') . ($caller['type'] ?? '') . ($caller['function'] ?? '')));

        $file = $caller['file'] ?? null;
        $line = $caller['line'] ?? null;

        LogEntry::create([
            'level'      => $level,
            'message'    => $message,
            'location'   => $resolvedLocation,
            'file'       => $file,
            'line'       => $line,
            'context'    => $context ?: null,
            'channel'    => $channel ?: ($context['channel'] ?? 'default'),
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'user_id'    => Auth::id(),
            'created_at' => now(),
        ]);
        
        if (rand(1, 100) === 1) { // 1% chance on each log
            $this->cleanup();
        }
    }

    protected function cleanup(): void
    {
        $retentionDays = config('scollio-logger.retention_days');

        if (! $retentionDays) {
            return;
        }

        $cutoffDate = Carbon::now()->subDays($retentionDays);
        LogEntry::where('created_at', '<', $cutoffDate)->delete();
    }

    public function emergency($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context, $location, $channel);
    }

    public function alert($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::ALERT, $message, $context, $location, $channel);
    }

    public function critical($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context, $location, $channel);
    }

    public function error($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::ERROR, $message, $context, $location, $channel);
    }

    public function warning($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::WARNING, $message, $context, $location, $channel);
    }

    public function notice($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::NOTICE, $message, $context, $location, $channel);
    }

    public function info($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::INFO, $message, $context, $location, $channel);
    }

    public function debug($message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $this->log(LogLevel::DEBUG, $message, $context, $location, $channel);
    }
}
