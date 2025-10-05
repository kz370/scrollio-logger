<?php

namespace Scollio\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Scollio\Facades\Logger;
use Throwable;

class ExceptionLogger
{
    public function handle(Request $request, Closure $next)
    {
        $config = config('scollio-logger.middleware.exception_logging', []);
        
        if (!$this->shouldLogExceptions($request, $config)) {
            return $next($request);
        }

        try {
            return $next($request);
        } catch (Throwable $e) {
            $this->logException($e, $request, $config);
            
            // Always rethrow to let Laravel's exception handler deal with it
            throw $e;
        }
    }

    private function shouldLogExceptions(Request $request, array $config): bool
    {
        if (!($config['enabled'] ?? false)) {
            return false;
        }

        $isApi = $request->is('api/*') || $request->wantsJson();
        
        if ($isApi && !($config['log_api_exceptions'] ?? true)) {
            return false;
        }
        
        if (!$isApi && !($config['log_web_exceptions'] ?? true)) {
            return false;
        }

        if ($this->shouldSkipRoute($request, $config['skip_routes'] ?? [])) {
            return false;
        }

        if (!empty($config['only_routes']) && !$this->matchesOnlyRoutes($request, $config['only_routes'])) {
            return false;
        }

        return true;
    }

    private function shouldSkipRoute(Request $request, array $skipRoutes): bool
    {
        $currentPath = $request->path();

        foreach ($skipRoutes as $pattern) {
            if ($currentPath === trim($pattern, '/')) {
                return true;
            }

            if ($request->is($pattern)) {
                return true;
            }

            if ($request->route() && $request->route()->getName()) {
                $routeName = $request->route()->getName();
                if (str_starts_with($routeName, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function matchesOnlyRoutes(Request $request, array $onlyRoutes): bool
    {
        $currentPath = $request->path();

        foreach ($onlyRoutes as $pattern) {
            if ($currentPath === trim($pattern, '/')) {
                return true;
            }

            if ($request->is($pattern)) {
                return true;
            }

            if ($request->route() && $request->route()->getName()) {
                $routeName = $request->route()->getName();
                if (str_starts_with($routeName, $pattern)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function logException(Throwable $e, Request $request, array $config): void
    {
        $exceptionType = get_class($e);
        
        if (!$this->shouldLogExceptionType($exceptionType, $config)) {
            return;
        }

        $logData = [
            'exception_type' => $exceptionType,
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
            $logData['request_data'] = $this->filterSensitiveData($request->all(), $config);
        }

        // Extract controller information
        if ($request->route()) {
            $action = $request->route()->getAction();
            if (isset($action['controller'])) {
                $logData['controller_action'] = $action['controller'];
            }
        }

        $level = $this->determineLogLevel($e, $config);
        $channel = $config['channel'] ?? 'exception_logging';

        Logger::$level(
            sprintf('[EXCEPTION] %s: %s at %s:%d', 
                class_basename($e), 
                $e->getMessage(), 
                basename($e->getFile()), 
                $e->getLine()
            ),
            $logData,
            'ExceptionLogger',
            $channel
        );
    }

    private function shouldLogExceptionType(string $exceptionType, array $config): bool
    {
        $ignoreExceptions = $config['ignore_exceptions'] ?? [];
        
        foreach ($ignoreExceptions as $ignoredType) {
            if ($exceptionType === $ignoredType || is_subclass_of($exceptionType, $ignoredType)) {
                return false;
            }
        }

        $onlyExceptions = $config['only_exceptions'] ?? [];
        
        if (!empty($onlyExceptions)) {
            foreach ($onlyExceptions as $allowedType) {
                if ($exceptionType === $allowedType || is_subclass_of($exceptionType, $allowedType)) {
                    return true;
                }
            }
            return false;
        }

        return true;
    }

    private function determineLogLevel(Throwable $e, array $config): string
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

    private function filterSensitiveData(array $data, array $config): array
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
