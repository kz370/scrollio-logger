<?php

namespace Scollio\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Scollio\Facades\Logger;

class GlobalRequestLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $config = config('scollio-logger.middleware.request_logging', []);
        $dashboardRoute = config('scollio-logger.dashboard.route', 'scollio-logs/dashboard');
        $globalEnabled = !empty($config['enabled']);

        // If global logging is disabled and no manual assignment, skip
        if (!$globalEnabled && !$this->isManuallyAssigned($request)) {
            return $next($request);
        }

        // Determine if this is an API or web request
        $isApi = $request->is('api/*') || $request->wantsJson();

        // Check if this request type should be logged
        $logApi = $config['log_api'] ?? true;
        $logWeb = $config['log_web'] ?? true;

        if (($isApi && !$logApi) || (!$isApi && !$logWeb)) {
            return $next($request);
        }

        // Skip certain routes to prevent recursion and improve performance
        $skipRoutes = $config['skip_routes'] ?? [
            'telescope/*',
            'horizon/*',
            '_debugbar/*',
        ];

        // Add dashboard routes with proper patterns
        $skipRoutes[] = $dashboardRoute;
        $skipRoutes[] = $dashboardRoute . '/*';
        $skipRoutes[] = trim($dashboardRoute, '/') . '/*'; // Handle leading/trailing slashes

        if ($this->shouldSkipRoute($request, $skipRoutes)) {
            return $next($request);
        }

        // Process the request first to get response info
        $response = $next($request);

        // Log the request
        $this->logRequest($request, $response, $isApi);

        return $response;
    }


    /**
     * Check if middleware was manually assigned to this route
     */
    private function isManuallyAssigned(Request $request): bool
    {
        // Simple check: if global is disabled but middleware runs, it's manual
        // You could add more sophisticated detection if needed
        return true; // If middleware runs and global is disabled, assume manual
    }

    /**
     * Log the request information
     */
    private function logRequest(Request $request, $response, bool $isApi): void
    {
        $userId = auth()->check() ? auth()->id() : null;
        $routeName = $request->route() ? $request->route()->getName() : null;

        $logData = [
            'type' => $isApi ? 'api' : 'web',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => $routeName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $userId,
            'status_code' => $response->getStatusCode(),
            'execution_time' => defined('LARAVEL_START') ?
                round((microtime(true) - LARAVEL_START) * 1000, 2) . 'ms' : 'N/A',
        ];

        // Add payload for non-GET requests if enabled
        $config = config('scollio-logger.middleware.request_logging', []);
        if (!empty($config['log_payload']) && !$request->isMethod('GET')) {
            $logData['payload'] = $this->filterSensitiveData($request->all());
        }

        // Add headers if enabled
        if (!empty($config['log_headers'])) {
            $logData['headers'] = $request->headers->all();
        }

        $message = sprintf(
            '[%s] %s %s - %d (%s)',
            strtoupper($logData['type']),
            $request->method(),
            $request->getPathInfo(),
            $response->getStatusCode(),
            $logData['execution_time']
        );

        $level = $this->getLogLevel($response->getStatusCode());
        $channel = 'request_logging';

        Logger::$level($message, $logData, 'GlobalRequestLogger', $channel);
    }

    /**
     * Check if route should be skipped
     */
    /**
     * Check if route should be skipped
     */
    private function shouldSkipRoute(Request $request, array $skipRoutes): bool
    {
        $currentPath = $request->path();

        foreach ($skipRoutes as $pattern) {
            // Handle exact matches
            if ($currentPath === trim($pattern, '/')) {
                return true;
            }

            // Handle wildcard patterns
            if ($request->is($pattern)) {
                return true;
            }

            // Handle patterns without leading slash
            if ($request->is(ltrim($pattern, '/'))) {
                return true;
            }

            // Handle route name matches (if applicable)
            if ($request->route() && $request->route()->getName()) {
                $routeName = $request->route()->getName();
                if (str_starts_with($routeName, 'scollio-logs.')) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * Filter sensitive data from request payload
     */
    private function filterSensitiveData(array $data): array
    {
        $sensitiveKeys = config('scollio-logger.middleware.request_logging.sensitive_keys', [
            'password',
            'password_confirmation',
            'token',
            'secret',
            'key',
            'api_key',
            'access_token',
            'refresh_token'
        ]);

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***FILTERED***';
            }
        }
        return $data;
    }

    /**
     * Get appropriate log level based on status code
     */
    private function getLogLevel(int $statusCode): string
    {
        if ($statusCode >= 500) {
            return 'error';
        } elseif ($statusCode >= 400) {
            return 'warning';
        } else {
            return 'info';
        }
    }
}
