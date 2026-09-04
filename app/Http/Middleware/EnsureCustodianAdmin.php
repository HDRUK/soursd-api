<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\CustodianUser;
use App\Http\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Restricts a route to CustodianUsers holding the CUSTODIAN_ADMIN permission,
 * and only for their own Custodian - determined from the CustodianUser named
 * by the route's {id} parameter, falling back to a `custodian_id` in the
 * request payload.
 */
class EnsureCustodianAdmin
{
    use Responses;

    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! $user->custodian_user_id) {
            return $this->ForbiddenResponse();
        }

        $callerCustodianUser = CustodianUser::find($user->custodian_user_id);

        if (! $callerCustodianUser) {
            return $this->ForbiddenResponse();
        }

        $isAdmin = $callerCustodianUser->userPermissions()
            ->whereHas('permission', fn ($query) => $query->where('name', 'CUSTODIAN_ADMIN'))
            ->exists();

        if (! $isAdmin) {
            return $this->ForbiddenResponse();
        }

        $targetCustodianId = $this->resolveTargetCustodianId($request);

        if ($targetCustodianId !== null && $targetCustodianId !== $callerCustodianUser->custodian_id) {
            return $this->ForbiddenResponse();
        }

        return $next($request);
    }

    /**
     * Resolve the Custodian ID the request is targeting from the route's {id}
     * parameter (an existing CustodianUser), falling back to a `custodian_id`
     * on the request payload. Returns null when neither is present/resolvable,
     * so callers can decide how to treat an unscoped request (e.g. creation).
     */
    private function resolveTargetCustodianId(Request $request): ?int
    {
        $routeId = $request->route('id');

        if ($routeId !== null) {
            return CustodianUser::find($routeId)?->custodian_id;
        }

        if ($request->filled('custodian_id')) {
            return (int) $request->input('custodian_id');
        }

        return null;
    }
}
