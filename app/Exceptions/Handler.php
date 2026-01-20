<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ThrottleRequestsException) {

            $retryAfter = $e->getHeaders()['Retry-After'] ?? null;

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again later.',
                    'retry_after_seconds' => $retryAfter,
                ], 429);
            }

            // Web fallback
            return redirect()
                ->back()
                ->withErrors([
                    'email' => 'Too many attempts. Please wait and try again.',
                ]);
        }

        return parent::render($request, $e);
    }
}
