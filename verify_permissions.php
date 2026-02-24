<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Helpers\PermissionHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// Mock session helper
function mockSession($roleId)
{
    session(['id_user_type' => $roleId]);
}

// Test Cases
$roles = [
    1 => 'Admin',
    2 => 'Petugas',
    3 => 'Masyarakat',
    5 => 'Eksekutif',
    6 => 'Super Moderator',
    7 => 'Super Admin',
];

$features = array_keys(config('permissions.features'));

echo "=== VERIFICATION START ===\n";

foreach ($roles as $roleId => $roleName) {
    mockSession($roleId);
    echo "\nRole: $roleName (ID: $roleId)\n";

    foreach ($features as $feature) {
        $hasAccess = PermissionHelper::check($feature);
        $status = $hasAccess ? "YES" : "NO ";
        echo "  - $feature: $status\n";
    }
}

echo "\n=== VERIFICATION END ===\n";
