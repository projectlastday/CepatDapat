<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RecordActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Perform actions after the response has been sent (terminable).
     * Only records GET requests to reduce noise.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Only record if user is logged in and request is GET
        if (!session('id_user') || !$request->isMethod('GET')) {
            return;
        }

        try {
            DB::table('history_activity_user')->insert([
                'id_user' => session('id_user'),
                'url' => mb_substr($request->fullUrl(), 0, 500),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to record activity: ' . $e->getMessage());
        }
    }
}
