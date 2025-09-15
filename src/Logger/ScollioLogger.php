<?php

namespace Kz370\ScollioLogger\Logger;

use Kz370\ScollioLogger\Models\LogEntry;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ScollioLogger implements LoggerInterface
{
    public function log($level, $message, array $context = [], ?string $location = null, string $channel = 'default'): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1] ?? [];

        $resolvedLocation = $location
            ?? ($context['location'] ?? (($caller['class'] ?? '') . ($caller['type'] ?? '') . ($caller['function'] ?? '')));

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
    }

    public function emergency($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::EMERGENCY, $message, $context, $location, $channel); 
    }
    public function alert($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::ALERT, $message, $context, $location, $channel); 
    }
    public function critical($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::CRITICAL, $message, $context, $location, $channel); 
    }
    public function error($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::ERROR, $message, $context, $location, $channel); 
    }
    public function warning($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::WARNING, $message, $context, $location, $channel); 
    }
    public function notice($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::NOTICE, $message, $context, $location, $channel); 
    }
    public function info($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::INFO, $message, $context, $location, $channel); 
    }
    public function debug($message, $location = null, array $context = [], string $channel = 'default'): void { 
        $this->log(LogLevel::DEBUG, $message, $context, $location, $channel); 
    }
}
