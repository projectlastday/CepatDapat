<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id('id_otp');
            $table->string('email')->nullable()->index();
            $table->string('telepon')->nullable()->index();

            $table->string('email_otp_hash')->nullable();
            $table->string('telepon_otp_hash')->nullable();

            $table->timestamp('email_expires_at')->nullable();
            $table->timestamp('telepon_expires_at')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('telepon_verified_at')->nullable();

            $table->timestamp('email_last_sent_at')->nullable();
            $table->timestamp('telepon_last_sent_at')->nullable();

            $table->unsignedInteger('email_attempts')->default(0);
            $table->unsignedInteger('telepon_attempts')->default(0);

            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['email', 'telepon']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
