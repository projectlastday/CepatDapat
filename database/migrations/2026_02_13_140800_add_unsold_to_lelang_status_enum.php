<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE `lelang` MODIFY COLUMN `status` ENUM('pending','accepted','rejected','open','sold','unsold','canceled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `lelang` MODIFY COLUMN `status` ENUM('pending','accepted','rejected','open','sold','canceled') DEFAULT 'pending'");
    }
};
