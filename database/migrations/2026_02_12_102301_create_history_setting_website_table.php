<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_setting_website', function (Blueprint $table) {
            $table->increments('id_history_setting');
            $table->integer('id_pelaku');
            $table->string('type');
            $table->text('data_lama')->nullable();
            $table->text('data_baru')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_setting_website');
    }
};
