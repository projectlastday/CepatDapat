<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HakAksesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('hak_akses')) {
            return;
        }

        $defaultMatrix = config('permissions.default_matrix', []);
        $now = now();
        $newRecords = [];

        foreach ($defaultMatrix as $roleId => $features) {
            foreach ($features as $feature) {
                // Check if already exists to avoid duplication errors if unique constraint missing
                $exists = DB::table('hak_akses')
                    ->where('id_user_type', $roleId)
                    ->where('feature', $feature)
                    ->exists();

                if (!$exists) {
                    $newRecords[] = [
                        'id_user_type' => $roleId,
                        'feature' => $feature,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($newRecords)) {
            DB::table('hak_akses')->insert($newRecords);
            $this->command->info('Seeded ' . count($newRecords) . ' permission records.');
        } else {
            $this->command->info('No new permissions to seed.');
        }
    }
}
