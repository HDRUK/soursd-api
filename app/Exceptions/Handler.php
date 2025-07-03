<?php

namespace App\Exceptions;

use Throwable;
use App\Services\AuditingService;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    public function report(Throwable $ex): void
    {
        app(AuditingService::class)->logException($ex);

        if (str_contains($ex->getMessage(), 'Header may not contain')) {
            \Log::error('Bad header value', [
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
            ]);
        }
        
        parent::report($ex);
    }
}
