<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Traits\Responses;
use App\Http\Traits\HmacSigning;

class VerifyGatewayHandoffSignature
{
    use Responses;
    use HmacSigning;

    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('x-signature');
        $secretKey = config('speedi.system.gateway_handoff_secret');

        if (!$signature || !$secretKey) {
            return $this->InvalidSignatureResponse();
        }

        if (!$this->verifyBase64RawSignature($request->route('code'), $secretKey, $signature)) {
            return $this->InvalidSignatureResponse();
        }

        return $next($request);
    }
}
