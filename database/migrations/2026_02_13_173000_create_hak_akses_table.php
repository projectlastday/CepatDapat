<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hak_akses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user_type')->index();
            $table->string('feature', 64)->index();
            $table->timestamps();

            $table->unique(['id_user_type', 'feature']);
        });

        // Seed with default matrix from config
        $matrix = config('permissions.default_matrix', []);
        $now = now();
        $data = [];

        foreach ($matrix as $roleId => $features) {
            foreach ($features as $feature) {
                $data[] = [
                    'id_user_type' => $roleId,
                    'feature' => $feature,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($data)) {
            \Illuminate\Support\Facades\DB::table('hak_akses')->insert($data);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_akses');
    }
};
