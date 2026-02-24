<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('history_activity_user', function (Blueprint $table) {
            $table->increments('id_activity_user');
            $table->integer('id_user');
            $table->string('url', 500);
            $table->timestamp('created_at')->useCurrent();

            $table->index('id_user');
            $table->index('created_at');
            $table->index(['id_user', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_activity_user');
    }
};
