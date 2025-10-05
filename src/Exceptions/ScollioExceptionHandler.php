<?php

namespace Scollio\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Request;
use Scollio\Facades\Logger;

class ScollioExceptionHandler
{
    /**
     * Report or log an exception
     */
    public static function report(Throwable $e): void
    {
        // Debug log to see if this is being called
        \Log::debug('ScollioExceptionHandler::report called', [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
        ]);

        $config = config('scollio-logger.middleware.exception_logging', []);

        if (!($config['enabled'] ?? false)) {
            \Log::debug('ScollioExceptionHandler: disabled in config');
            return;
        }

        if (!self::shouldLogException($e, $config)) {
            \Log::debug('ScollioExceptionHandler: should not log this exception');
            return;
        }

        self::logException($e, $config);
        \Log::debug('ScollioExceptionHandler: exception logged');
    }

    /**
     * Check if exception should be logged
     */
    private static function shouldLogException(Throwable $e, array $config): bool
    {
        $request = request();
        $isApi = $request->is('api/*') || $request->wantsJson();

        if ($isApi && !($config['log_api_exceptions'] ?? true)) {
            return false;
        }

        if (!$isApi && !($config['log_web_exceptions'] ?? true)) {
            return false;
        }

        // Check if exception type should be ignored
        $ignoreExceptions = $config['ignore_exceptions'] ?? [];
        foreach ($ignoreExceptions as $ignoredType) {
            if ($e instanceof $ignoredType) {
                return false;
            }
        }

        // Check if only specific exception types should be logged
        $onlyExceptions = $config['only_exceptions'] ?? [];
        if (!empty($onlyExceptions)) {
            $shouldLog = false;
            foreach ($onlyExceptions as $allowedType) {
                if ($e instanceof $allowedType) {
                    $shouldLog = true;
                    break;
                }
            }
            if (!$shouldLog) {
                return false;
            }
        }

        return true;
    }

    /**
     * Log the exception
     */
    private static function logException(Throwable $e, array $config): void
    {
        $request = request();

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

        if ($config['log_stack_trace'] ?? false) {
            $logData['stack_trace'] = $e->getTraceAsString();
        }

        if ($config['log_request_data'] ?? false) {
            $logData['request_data'] = self::filterSensitiveData($request->all(), $config);
        }

        // Extract controller information from the request
        if ($request->route()) {
            $action = $request->route()->getAction();
            if (isset($action['controller'])) {
                $logData['controller_action'] = $action['controller'];
            }
        }

        $level = self::determineLogLevel($e, $config);
        $channel = $config['channel'] ?? 'exception_logging';

        Logger::$level(
            sprintf(
                '[EXCEPTION] %s: %s at %s:%d',
                class_basename($e),
                $e->getMessage(),
                basename($e->getFile()),
                $e->getLine()
            ),
            $logData,
            'ScollioExceptionHandler',
            $channel
        );
    }

    /**
     * Determine log level based on exception type
     */
    private static function determineLogLevel(Throwable $e, array $config): string
    {
        $levelMapping = $config['exception_levels'] ?? [
            \Illuminate\Database\QueryException::class => 'critical',
            \Illuminate\Database\Eloquent\ModelNotFoundException::class => 'warning',
            \Illuminate\Validation\ValidationException::class => 'info',
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class => 'notice',
            \Symfony\Component\HttpKernel\Exception\HttpException::class => 'warning',
        ];

        foreach ($levelMapping as $exceptionClass => $level) {
            if ($e instanceof $exceptionClass) {
                return $level;
            }
        }

        return 'error';
    }

    /**
     * Filter sensitive data from request
     */
    private static function filterSensitiveData(array $data, array $config): array
    {
        $sensitiveKeys = $config['sensitive_keys'] ?? [
            'password',
            'password_confirmation',
            'token',
            'secret',
            'key',
            'api_key',
            'access_token',
            'refresh_token',
            'client_secret',
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***FILTERED***';
            }
        }

        return $data;
    }
}
