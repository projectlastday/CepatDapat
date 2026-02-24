<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PermissionHelper
{
    /**
     * Check if the user (or specific role) has access to a feature.
     */
    public static function check(string $feature, ?int $userType = null): bool
    {
        $userType = $userType ?? session('id_user_type');

        if (!$userType) {
            return false;
        }

        // 1. Super Admin Override
        if ($userType === config('permissions.super_admin_role', 7)) {
            return true;
        }

        // 2. Validate Feature Exists
        $allFeatures = config('permissions.features', []);
        if (!array_key_exists($feature, $allFeatures)) {
            Log::warning("Permission check checking unknown feature: {$feature}");
            return false;
        }

        // 3. Check Cache
        return self::hasFeature($userType, $feature);
    }

    /**
     * Check if user has ANY of the given features.
     */
    public static function checkAny(array $features, ?int $userType = null): bool
    {
        foreach ($features as $feature) {
            if (self::check($feature, $userType)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Internal logic to check DB with caching.
     */
    private static function hasFeature(int $roleId, string $feature): bool
    {
        $key = "hak_akses:role:{$roleId}";

        $features = Cache::remember($key, 600, function () use ($roleId) {
            // Fallback to config if table is empty (safety net)
            if (DB::table('hak_akses')->count() === 0) {
                return config("permissions.default_matrix.{$roleId}", []);
            }

            return DB::table('hak_akses')
                ->where('id_user_type', $roleId)
                ->pluck('feature')
                ->toArray();
        });

        return in_array($feature, $features);
    }

    /**
     * Clear cache for specific roles.
     */
    public static function clearRoleCache(array $roleIds): void
    {
        foreach ($roleIds as $roleId) {
            Cache::forget("hak_akses:role:{$roleId}");
        }
    }
}
