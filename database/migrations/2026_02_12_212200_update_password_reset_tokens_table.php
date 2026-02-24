<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Check if table exists first
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('email')->nullable()->index();
                $table->string('telepon')->nullable()->index();
                $table->string('token')->index();
                $table->timestamp('created_at')->nullable();
            });
            return;
        }

        // Modify existing table
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Drop the primary key on email
            try {
                $table->dropPrimary('email');
            } catch (\Exception $e) {
                // Primary may not exist or already dropped
            }
        });

        // Clear existing data to avoid constraint issues during migration
        DB::table('password_reset_tokens')->truncate();

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Add auto-increment id
            $table->id()->first();
            // Make email nullable
            $table->string('email')->nullable()->change();
            // Add telepon column
            $table->string('telepon')->nullable()->after('email');
            // Add indexes
            $table->index('email');
            $table->index('telepon');
            $table->index('token');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('password_reset_tokens', 'telepon')) {
            Schema::table('password_reset_tokens', function (Blueprint $table) {
                $table->dropColumn('telepon');
                $table->dropColumn('id');
            });
        }
    }
};
