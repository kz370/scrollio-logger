<?php

namespace Kz370\ScollioLogger\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Kz370\ScollioLogger\Models\ScollioLogger;
use Kz370\ScollioLogger\Support\ScollioLogHelper;

class TrafficLogger
{
    protected array $except = [];
    protected array $sensitiveExactKeys = [];
    protected array $sensitivePartialKeys = [];
    protected bool $logWebRoutes;
    protected bool $logApiRoutes;
    protected array $ignoreStatusCodes = [];
    protected array $onlyStatusCodes = [];

    public function __construct()
    {
        $this->except               = config('scollio-logger.skip_routes', []);
        $this->sensitiveExactKeys   = config('scollio-logger.sensitive_exact_keys', []);
        $this->sensitivePartialKeys = config('scollio-logger.sensitive_partial_keys', []);
        $this->logWebRoutes         = (bool) config('scollio-logger.log_web_routes', true);
        $this->logApiRoutes         = (bool) config('scollio-logger.log_api_routes', true);
        $this->ignoreStatusCodes    = config('scollio-logger.ignore_status_codes', []);
        $this->onlyStatusCodes      = config('scollio-logger.only_status_codes', []);
    }

    public function handle(Request $request, Closure $next)
    {
        if (
            ScollioLogHelper::shouldSkip($request, $this->except) ||
            ScollioLogHelper::shouldSkipBasedOnGroup($request, $this->logApiRoutes, $this->logWebRoutes)
        ) {
            return $next($request);
        }

        $start = microtime(true);

        $log = new ScollioLogger([
            'method'       => $request->method(),
            'url'          => $request->fullUrl(),
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'headers'      => $request->headers->all(),
            'body'         => ScollioLogHelper::sanitizeBody($request->all(), $this->sensitiveExactKeys, $this->sensitivePartialKeys),
            'requested_at' => now(),
            'user_id'      => optional($request->user())->id,
            'session_id'   => $request->hasSession() ? $request->session()->getId() : null,
            'request_id'   => (string) Str::uuid(),
            'route_action' => ScollioLogHelper::getRouteAction($request),
        ]);

        try {
            $response = $next($request);
        } catch (\Throwable $e) {
            $log->exception_message = $e->getMessage();
            $log->exception_file    = $e->getFile();
            $log->exception_line    = $e->getLine();
            $log->exception_trace   = $e->getTraceAsString();
            $log->status_code       = 500;
            $log->responded_at      = now();
            $log->duration_ms       = (microtime(true) - $start) * 1000;
            $log->save();
            throw $e;
        }

        $log->status_code = $response->getStatusCode();

        if (ScollioLogHelper::shouldSkipByStatus($log->status_code, $this->onlyStatusCodes, $this->ignoreStatusCodes)) {
            return $response;
        }

        $log->response_headers = $response->headers->all();
        $log->response_body    = ScollioLogHelper::getResponseBody($response);
        $log->responded_at     = now();
        $log->duration_ms      = (microtime(true) - $start) * 1000;
        $log->save();

        return $response;
    }
}
