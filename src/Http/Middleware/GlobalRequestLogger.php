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
        $skipRoutes[] = trim($dashboardRoute, '/') . '/*';

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
        return true;
    }

    /**
     * Log the request information
     */
    private function logRequest(Request $request, $response, bool $isApi): void
    {
        $userId = auth()->check() ? auth()->id() : null;
        $routeName = $request->route() ? $request->route()->getName() : null;

        // Extract controller and action information
        $controllerInfo = $this->extractControllerInfo($request);

        $logData = [
            'type' => $isApi ? 'api' : 'web',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'route_name' => $routeName,
            'controller' => $controllerInfo['controller'],
            'action' => $controllerInfo['action'],
            'controller_action' => $controllerInfo['controller_action'],
            'file' => $controllerInfo['file'],
            'line' => $controllerInfo['line'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $userId,
            'status_code' => $response->getStatusCode(),
            'execution_time' => defined('LARAVEL_START') ?
                round((microtime(true) - LARAVEL_START) * 1000, 2) . 'ms' : 'N/A',
        ];

        // Add query parameters for GET requests
        if ($request->isMethod('GET') && !empty($request->query())) {
            $logData['query_params'] = $request->query();
        }

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

        Logger::$level($message, $logData, $controllerInfo['location'], $channel);
    }

    /**
     * Extract controller and action information from the request
     */
    private function extractControllerInfo(Request $request): array
    {
        $info = [
            'controller' => null,
            'action' => null,
            'controller_action' => null,
            'file' => null,
            'line' => null,
            'location' => 'GlobalRequestLogger',
        ];

        if (!$request->route()) {
            return $info;
        }

        $action = $request->route()->getAction();

        // Handle controller@method format
        if (isset($action['controller'])) {
            $controllerAction = $action['controller'];
            $info['controller_action'] = $controllerAction;

            if (str_contains($controllerAction, '@')) {
                [$controller, $method] = explode('@', $controllerAction);
                $info['controller'] = $controller;
                $info['action'] = $method;
                $info['location'] = $controllerAction;

                try {
                    $reflection = new \ReflectionClass($controller);
                    $info['file'] = $reflection->getFileName();

                    if ($reflection->hasMethod($method)) {
                        $methodReflection = $reflection->getMethod($method);
                        $info['line'] = $methodReflection->getStartLine();
                    }
                } catch (\ReflectionException $e) {
                    // Silently handle reflection errors
                }
            }
        }
        // Handle invokable controllers
        elseif (isset($action['uses']) && is_string($action['uses'])) {
            $info['controller'] = $action['uses'];
            $info['action'] = '__invoke';
            $info['controller_action'] = $action['uses'] . '@__invoke';
            $info['location'] = $info['controller_action'];

            try {
                $reflection = new \ReflectionClass($action['uses']);
                $info['file'] = $reflection->getFileName();

                if ($reflection->hasMethod('__invoke')) {
                    $methodReflection = $reflection->getMethod('__invoke');
                    $info['line'] = $methodReflection->getStartLine();
                }
            } catch (\ReflectionException $e) {
                // Silently handle reflection errors
            }
        }
        // Handle closures
        elseif (isset($action['uses']) && $action['uses'] instanceof \Closure) {
            $reflection = new \ReflectionFunction($action['uses']);
            $info['controller'] = 'Closure';
            $info['action'] = 'anonymous';
            $info['controller_action'] = 'Closure';
            $info['file'] = $reflection->getFileName();
            $info['line'] = $reflection->getStartLine();
            $info['location'] = 'Closure';
        }

        return $info;
    }

    /**
     * Check if route should be skipped
     */
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

            if ($request->is(ltrim($pattern, '/'))) {
                return true;
            }

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
