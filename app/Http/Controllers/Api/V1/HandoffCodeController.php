<?php

namespace App\Http\Controllers\Api\V1;

use Keycloak;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\Responses;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class HandoffCodeController extends Controller
{
    use Responses;

    private const TTL_SECONDS = 60;
    private const CACHE_PREFIX = 'gateway_handoff:';

    /**
     * Called by speedi-as-web once it has completed the Keycloak code exchange for a
     * Gateway-originated login/registration. Stores the caller's Keycloak claims behind
     * a short-lived, single-use opaque code that Gateway redeems server-to-server.
     */
    public function store(Request $request): JsonResponse
    {
        $response = Keycloak::getUserInfo($request->headers->get('Authorization'));

        if (!$response->successful()) {
            return $this->UnauthorisedResponse();
        }

        $code = Str::random(40);

        Cache::put(self::CACHE_PREFIX . $code, $response->json(), self::TTL_SECONDS);

        return $this->CreatedResponse(['code' => $code]);
    }

    /**
     * Called by Gateway's backend. Atomically fetches and deletes the claims so a code
     * can only ever be redeemed once.
     */
    public function redeem(string $code): JsonResponse
    {
        $claims = Cache::pull(self::CACHE_PREFIX . $code);

        if (!$claims) {
            return $this->NotFoundResponse();
        }

        return $this->OKResponse($claims);
    }
}
