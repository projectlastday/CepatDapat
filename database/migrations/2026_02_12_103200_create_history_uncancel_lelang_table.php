<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_uncancel_lelang', function (Blueprint $table) {
            $table->increments('id_history_uncancel');
            $table->integer('id_pelaku');
            $table->integer('id_lelang');
            $table->text('alasan');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_uncancel_lelang');
    }
};
