<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        // Akses hanya untuk Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('setting_view')) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $logo = DB::table('setting_website')->where('key', 'logo')->value('value');
        $hakAksesData = $this->getHakAkses();

        return view('admin.setting_website', [
            'logo' => $logo,
            'hak_akses' => $hakAksesData,
        ]);
    }

    public function update_logo(Request $request)
    {
        // Akses hanya untuk Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('setting_view')) {
            return redirect()->back()->with('error', 'Hanya Super Admin yang dapat mengubah setting website.');
        }

        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        // Get old logo
        $old_logo = DB::table('setting_website')->where('key', 'logo')->value('value');

        // Upload new logo
        $file = $request->file('logo');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('img_website'), $filename);
        $new_logo = 'img_website/' . $filename;

        // Update setting
        DB::table('setting_website')->where('key', 'logo')->update([
            'value' => $new_logo,
        ]);

        // Log history
        DB::table('history_setting_website')->insert([
            'id_pelaku' => session('id_user'),
            'type' => 'Change Logo',
            'data_lama' => $old_logo,
            'data_baru' => $new_logo,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Logo website berhasil diperbarui.');
    }

    /**
     * Stream a full SQL dump of the database as a download.
     * Super Admin only. Logs the action to history_setting_website.
     */
    public function backup(Request $request, DatabaseBackupService $backupService)
    {
        // Akses hanya untuk Super Admin (7)
        if (!\App\Helpers\PermissionHelper::check('setting_view')) {
            return redirect()->back()->with('error', 'Hanya Super Admin yang dapat melakukan backup.');
        }

        try {
            $filename = 'backup_cepatdapat_' . now()->format('Y-m-d_H-i-s') . '.sql';

            // Log history
            DB::table('history_setting_website')->insert([
                'id_pelaku' => session('id_user'),
                'type' => 'Backup Database',
                'data_lama' => null,
                'data_baru' => $filename,
                'created_at' => Carbon::now(),
            ]);

            return $backupService->streamDownload($filename);
        } catch (\Throwable $e) {
            Log::error('Database backup failed: ' . $e->getMessage(), [
                'user' => session('id_user'),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal membuat backup database. Silakan coba lagi.');
        }
    }
    /**
     * Get data for Hak Akses tab.
     */
    private function getHakAkses()
    {
        // Get all features from config
        $features = config('permissions.features');

        // Get editable roles
        $editableRoleIds = config('permissions.editable_roles');
        $allRoleLabels = config('permissions.roles_labels');

        $roles = [];
        foreach ($editableRoleIds as $id) {
            if (isset($allRoleLabels[$id])) {
                $roles[$id] = $allRoleLabels[$id];
            }
        }

        // Build current matrix
        // [feature_key => [role_id => bool]]
        $matrix = [];

        // Pre-fill with false
        foreach ($features as $fKey => $fMeta) {
            foreach ($roles as $rId => $rLabel) {
                $matrix[$fKey][$rId] = false;
            }
        }

        // Load from DB
        $dbPermissions = DB::table('hak_akses')->get();
        foreach ($dbPermissions as $perm) {
            if (isset($matrix[$perm->feature][$perm->id_user_type])) {
                $matrix[$perm->feature][$perm->id_user_type] = true;
            }
        }

        return [
            'features' => $features,
            'roles' => $roles,
            'matrix' => $matrix,
        ];
    }

    /**
     * Update Hak Akses configuration.
     * Only Super Admin (7).
     */
    public function updateHakAkses(Request $request)
    {
        // 1. Security Check
        if (session('id_user_type') != 7) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Validation
        // permissions array: [feature_key => [role_id => 'on']]
        $request->validate([
            'permissions' => 'array',
        ]);

        $submittedPermissions = $request->input('permissions', []);
        $editableRoles = config('permissions.editable_roles');
        $validFeatures = array_keys(config('permissions.features'));

        // 3. Prepare Snapshot for History (Get current state)
        $oldState = DB::table('hak_akses')
            ->whereIn('id_user_type', $editableRoles)
            ->get()
            ->map(function ($item) {
                return $item->id_user_type . ':' . $item->feature;
            })
            ->toArray();

        // 4. Transaction
        try {
            DB::beginTransaction();

            // Wipe permissions for editable roles
            DB::table('hak_akses')
                ->whereIn('id_user_type', $editableRoles)
                ->delete();

            $newRecords = [];
            $now = Carbon::now();

            foreach ($submittedPermissions as $feature => $roles) {
                // Ignore invalid features
                if (!in_array($feature, $validFeatures)) {
                    continue;
                }

                foreach ($roles as $roleId => $value) {
                    $roleId = (int) $roleId;

                    // Security: Ignore if role not in editable list (e.g. trying to change Super Admin)
                    if (!in_array($roleId, $editableRoles)) {
                        continue;
                    }

                    $newRecords[] = [
                        'id_user_type' => $roleId,
                        'feature' => $feature,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($newRecords)) {
                DB::table('hak_akses')->insert($newRecords);
            }

            // 5. Clear Cache
            \App\Helpers\PermissionHelper::clearRoleCache($editableRoles);

            // 6. Log History
            $newState = collect($newRecords)->map(function ($item) {
                return $item['id_user_type'] . ':' . $item['feature'];
            })->toArray();

            DB::table('history_setting_website')->insert([
                'id_pelaku' => session('id_user'),
                'type' => 'Update Hak Akses',
                'data_lama' => json_encode($oldState),
                'data_baru' => json_encode($newState),
                'created_at' => $now,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Hak Akses berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Hak Akses failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui hak akses.');
        }
    }
}


