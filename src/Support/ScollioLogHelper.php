<?php

namespace Kz370\ScollioLogger\Support;

use Illuminate\Http\Request;

class ScollioLogHelper
{
    public static function shouldSkip(Request $request, array $except): bool
    {
        $except = array_merge($except, [config('scollio-logger.dashboard.route')]);
        foreach ($except as $pattern) {
            if ($request->is($pattern) || $request->is($pattern . '/*')) {
                return true;
            }
        }
        return false;
    }

    public static function shouldSkipBasedOnGroup(Request $request, bool $logApi, bool $logWeb): bool
    {
        $middlewares = $request->route()?->gatherMiddleware() ?? [];

        $isApi = in_array('api', $middlewares, true);
        $isWeb = in_array('web', $middlewares, true);

        if ($isApi && ! $logApi) return true;
        if ($isWeb && ! $logWeb) return true;

        return false;
    }

    public static function shouldSkipByStatus(int $status, array $only, array $ignore): bool
    {
        if (!empty($only)) {
            return ! in_array($status, $only, true);
        }
        return in_array($status, $ignore, true);
    }

    public static function sanitizeBody(array $data, array $exactKeys, array $partialKeys): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower($key);

            $isExactMatch = in_array($normalizedKey, array_map('strtolower', $exactKeys), true);
            $isPartialMatch = collect($partialKeys)
                ->contains(fn($pattern) => str_contains($normalizedKey, strtolower($pattern)));

            if ($isExactMatch || $isPartialMatch) {
                $filtered[$key] = config('scollio-logger.filtered_string', '[FILTERED]');
                continue;
            }

            if (is_array($value)) {
                $filtered[$key] = self::sanitizeBody($value, $exactKeys, $partialKeys);
            } else {
                $filtered[$key] = is_string($value) && strlen($value) > 10000
                    ? substr($value, 0, 10000) . '... [truncated]'
                    : $value;
            }
        }

        return $filtered;
    }

    public static function getResponseBody($response): string
    {
        if (! method_exists($response, 'getContent')) {
            return '[streamed or binary response]';
        }

        try {
            $content = $response->getContent();
            return strlen($content) > 50000
                ? substr($content, 0, 50000) . '... [truncated]'
                : $content;
        } catch (\Throwable $e) {
            return '[unavailable: ' . get_class($response) . ']';
        }
    }

    public static function getRouteAction(Request $request): ?string
    {
        try {
            $route = $request->route();
            if (! $route) return null;
            $action = $route->getActionName();
            if (is_string($action)) return $action;
            if (is_array($action)) return json_encode($action);
            return '[unknown action]';
        } catch (\Throwable $e) {
            return '[unavailable: ' . $e->getMessage() . ']';
        }
    }
}
