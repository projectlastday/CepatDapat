<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\PermissionHelper;

class EnsureFeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        if (!PermissionHelper::check($feature)) {
            // Log for debugging
            // \Illuminate\Support\Facades\Log::info("Access denied for user " . session('id_user') . " to feature {$feature}");

            // Return redirect to catalog with error message
            return redirect()->route('catalog')->with('error', 'Anda tidak memiliki hak akses untuk fitur ini.');
        }

        return $next($request);
    }
}
