<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('setting_website', function (Blueprint $table) {
            $table->increments('id_setting');
            $table->string('key')->unique();
            $table->text('value')->nullable();
        });

        // Seed default logo
        DB::table('setting_website')->insert([
            'key' => 'logo',
            'value' => 'assets/images/CepatDapat.png',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_website');
    }
};
