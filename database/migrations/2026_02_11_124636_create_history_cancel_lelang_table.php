<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_cancel_lelang', function (Blueprint $table) {
            $table->id('id_cancell');
            $table->unsignedBigInteger('id_pelaku');
            $table->unsignedBigInteger('id_lelang');
            $table->text('alasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_cancel_lelang');
    }
};
