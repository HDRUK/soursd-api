<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class TerminateRequest
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $route = $request->route();
        $action = $route ? $route->getActionName() : null;

        if (is_null($request->route())) {
            gc_collect_cycles();
            Log::info('Memory usage request without route :: ' . json_encode($request) . ' :: response :: ' . json_encode($response));
            return null;
        }

        $memory = memory_get_usage(true) / 1024 / 1024;
        $peak = memory_get_peak_usage(true) / 1024 / 1024;
        $collected = gc_collect_cycles();

        Log::info('Memory usage after request', [
            'memory_usage' => round($memory, 2) . ' MB',
            'memory_peak_usage' => round($peak, 2) . ' MB',
            'action' => $action,
            'collected_cycles' => $collected,
        ]);
    }
}
