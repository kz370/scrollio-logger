<?php

namespace Scollio\Support;

use Throwable;
use Scollio\Facades\Logger;

class ScollioException
{
    /**
     * Log an exception that was caught in a try-catch block
     */
    public static function log(Throwable $e): void
    {
        $config = config('scollio-logger.middleware.exception_logging', []);

        if (!($config['enabled'] ?? false)) {
            return;
        }

        $request = request();
        $isApi = $request->is('api/*') || $request->wantsJson();

        if ($isApi && !($config['log_api_exceptions'] ?? true)) {
            return;
        }

        if (!$isApi && !($config['log_web_exceptions'] ?? true)) {
            return;
        }

        $logData = [
            'exception_type' => get_class($e),
            'exception_message' => $e->getMessage(),
            'exception_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_id' => auth()->check() ? auth()->id() : null,
        ];

        if ($config['log_stack_trace'] ?? true) {
            $logData['stack_trace'] = $e->getTraceAsString();
        }

        if ($config['log_request_data'] ?? true) {
            $sensitiveKeys = $config['sensitive_keys'] ?? [];
            $requestData = $request->all();
            
            foreach ($sensitiveKeys as $key) {
                if (isset($requestData[$key])) {
                    $requestData[$key] = '***FILTERED***';
                }
            }
            
            $logData['request_data'] = $requestData;
        }

        // Extract controller information
        if ($request->route()) {
            $action = $request->route()->getAction();
            if (isset($action['controller'])) {
                $logData['controller_action'] = $action['controller'];
            }
        }

        // Determine log level
        $levelMapping = $config['exception_levels'] ?? [
            \Illuminate\Database\QueryException::class => 'critical',
            \Illuminate\Database\Eloquent\ModelNotFoundException::class => 'warning',
            \Illuminate\Validation\ValidationException::class => 'info',
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'notice',
            \Symfony\Component\HttpKernel\Exception\HttpException::class => 'warning',
        ];

        $level = 'error';
        foreach ($levelMapping as $exceptionClass => $mappedLevel) {
            if ($e instanceof $exceptionClass) {
                $level = $mappedLevel;
                break;
            }
        }

        $channel = $config['channel'] ?? 'exception_logging';

        // Get caller information
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $backtrace[1] ?? [];
        $location = ($caller['class'] ?? 'Unknown') . ($caller['type'] ?? '::') . ($caller['function'] ?? 'unknown');

        Logger::$level(
            sprintf('[EXCEPTION] %s: %s',
                class_basename($e),
                $e->getMessage()
            ),
            $logData,
            $location,
            $channel
        );
    }
}
