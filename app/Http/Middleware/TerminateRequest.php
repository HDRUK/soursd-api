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
        if (is_null($request->route())) {
            return;
        }

        $memoryBefore = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
        $collected = gc_collect_cycles();
        $memoryAfter = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';

        Log::info('TerminateRequest', [
            'request' => $request->route()->getActionName(),
            'memory_before' => $memoryBefore,
            'collected' => $collected,
            'memory_after' => $memoryAfter,
        ]);
    }
}
